<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\Metrica;
use App\Models\SessaoEstudo;
use App\Models\User;
use App\Services\CronogramaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Testes para CronogramaService::gerar()
 *
 * Cenários cobertos:
 * - Retorna array vazio quando usuário não tem assuntos
 * - Gera sessões dentro do intervalo de datas correto
 * - Respeita o horário semanal (dias com 0 horas não recebem sessões)
 * - Limpa sessões não finalizadas quando $limpar=true
 * - Não limpa sessões quando $limpar=false
 * - Não limpa sessões já finalizadas mesmo com $limpar=true
 * - Gera sessão de teoria para assunto com teoria não finalizada
 * - Gera sessão de exercicio/revisao para assunto com teoria finalizada
 * - Respeita o tipo restrito do assunto (ex: só teoria)
 * - Prioriza assunto com maior taxa de erros (error_rate)
 * - $dias <= 0 usa fallback de 15 dias
 * - Acumula sessões existentes no cálculo de minutos do dia
 * - Retorna total correto de sessões criadas
 * - Intervalo de datas correto no retorno
 */
class CronogramaServiceTest extends TestCase
{
    use RefreshDatabase;

    private CronogramaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CronogramaService;
    }

    private function makeUser(array $horario = []): User
    {
        $defaultHorario = [
            'domingo' => 0,
            'segunda' => 2,
            'terca'   => 2,
            'quarta'  => 2,
            'quinta'  => 2,
            'sexta'   => 2,
            'sabado'  => 0,
        ];

        return User::factory()->create([
            'horario_semanal' => array_merge($defaultHorario, $horario),
        ]);
    }

    private function makeAssunto(User $user, array $attrs = []): Assunto
    {
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        return Assunto::factory()->create(array_merge([
            'materia_id'        => $materia->id,
            'teoria_finalizada' => false,
            'tipo'              => ['teoria', 'exercicio', 'revisao'],
        ], $attrs));
    }

    // -----------------------------------------------------------------------
    // Sem assuntos
    // -----------------------------------------------------------------------

    public function test_retorna_vazio_quando_usuario_nao_tem_assuntos(): void
    {
        $user   = $this->makeUser();
        $inicio = Carbon::parse('2025-01-06'); // segunda

        $result = $this->service->gerar($user, $inicio, 5);

        $this->assertSame(0, $result['total']);
        $this->assertEmpty($result['sessoes']);
        $this->assertSame('2025-01-06', $result['inicio']);
        $this->assertSame('2025-01-10', $result['fim']);
    }

    // -----------------------------------------------------------------------
    // Datas e intervalo
    // -----------------------------------------------------------------------

    public function test_intervalo_de_datas_correto_no_retorno(): void
    {
        $user   = $this->makeUser();
        $inicio = Carbon::parse('2025-03-03');

        $result = $this->service->gerar($user, $inicio, 7);

        $this->assertSame('2025-03-03', $result['inicio']);
        $this->assertSame('2025-03-09', $result['fim']);
    }

    public function test_dias_invalido_usa_fallback_de_15(): void
    {
        $user   = $this->makeUser();
        $inicio = Carbon::parse('2025-01-06');

        $result = $this->service->gerar($user, $inicio, 0);

        $fim = Carbon::parse($result['fim']);
        $this->assertEquals(14, Carbon::parse($result['inicio'])->diffInDays($fim));
    }

    // -----------------------------------------------------------------------
    // Respeita horário semanal
    // -----------------------------------------------------------------------

    public function test_dias_com_zero_horas_nao_recebem_sessoes(): void
    {
        // Apenas segunda tem horas disponíveis
        $user = $this->makeUser([
            'segunda' => 1,
            'terca'   => 0,
            'quarta'  => 0,
            'quinta'  => 0,
            'sexta'   => 0,
        ]);
        $this->makeAssunto($user);

        // Semana começa numa segunda (2025-01-06)
        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 5);

        $datas = collect($result['sessoes'])
            ->pluck('data')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values()
            ->toArray();

        $this->assertContains('2025-01-06', $datas);

        foreach (['2025-01-07', '2025-01-08', '2025-01-09', '2025-01-10'] as $data) {
            $this->assertNotContains($data, $datas);
        }
    }

    public function test_gera_sessoes_apenas_nos_dias_com_horas_disponiveis(): void
    {
        $user = $this->makeUser(['segunda' => 2, 'terca' => 0, 'quarta' => 2]);
        $this->makeAssunto($user);

        $inicio = Carbon::parse('2025-01-06'); // segunda
        $result = $this->service->gerar($user, $inicio, 3);

        $datas = collect($result['sessoes'])->pluck('data')->unique()->values()->toArray();
        $this->assertNotContains('2025-01-07', $datas); // terça sem horas
    }

    // -----------------------------------------------------------------------
    // limpar sessões
    // -----------------------------------------------------------------------

    public function test_limpar_true_remove_sessoes_nao_finalizadas(): void
    {
        $user    = $this->makeUser();
        $assunto = $this->makeAssunto($user);
        $inicio  = Carbon::parse('2025-01-06');

        $sessaoAntiga = SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
            'data'       => '2025-01-06',
            'finalizado' => false,
            'tipo'       => 'teoria',
            'horas'      => 0.5,
        ]);

        $this->service->gerar($user, $inicio, 5, limpar: true);

        $this->assertDatabaseMissing('sessoes_estudo', [
            'id' => $sessaoAntiga->id,
        ]);
    }

    public function test_limpar_true_preserva_sessoes_finalizadas(): void
    {
        $user    = $this->makeUser();
        $assunto = $this->makeAssunto($user);
        $inicio  = Carbon::parse('2025-01-06');

        SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
            'data'       => '2025-01-06',
            'finalizado' => true,
            'tipo'       => 'teoria',
            'horas'      => 0.5,
        ]);

        $this->service->gerar($user, $inicio, 5, limpar: true);

        $this->assertDatabaseHas('sessoes_estudo', [
            'assunto_id' => $assunto->id,
            'data'       => '2025-01-06',
            'finalizado' => true,
        ]);
    }

    public function test_limpar_false_preserva_sessoes_nao_finalizadas(): void
    {
        $user    = $this->makeUser();
        $assunto = $this->makeAssunto($user);
        $inicio  = Carbon::parse('2025-01-06');

        SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
            'data'       => '2025-01-06',
            'finalizado' => false,
            'tipo'       => 'teoria',
            'horas'      => 0.5,
        ]);

        $this->service->gerar($user, $inicio, 5, limpar: false);

        $this->assertDatabaseHas('sessoes_estudo', [
            'assunto_id' => $assunto->id,
            'data'       => '2025-01-06',
            'finalizado' => false,
            'horas'      => 0.5,
        ]);
    }

    // -----------------------------------------------------------------------
    // Tipos de sessão
    // -----------------------------------------------------------------------

    public function test_gera_teoria_quando_teoria_nao_finalizada(): void
    {
        $user    = $this->makeUser();
        $assunto = $this->makeAssunto($user, [
            'teoria_finalizada' => false,
            'tipo'              => ['teoria'],
        ]);

        $inicio = Carbon::parse('2025-01-06'); // segunda
        $result = $this->service->gerar($user, $inicio, 1);

        $tipos = collect($result['sessoes'])->pluck('tipo')->toArray();
        $this->assertContains('teoria', $tipos);
        $this->assertNotContains('exercicio', $tipos);
    }

    public function test_nao_gera_teoria_quando_teoria_ja_finalizada(): void
    {
        $user    = $this->makeUser();
        $assunto = $this->makeAssunto($user, [
            'teoria_finalizada' => true,
            'tipo'              => ['teoria', 'exercicio', 'revisao'],
        ]);

        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 1);

        $tipos = collect($result['sessoes'])->pluck('tipo')->toArray();
        $this->assertNotContains('teoria', $tipos);
    }

    public function test_respeita_tipo_restrito_do_assunto(): void
    {
        $user    = $this->makeUser(['segunda' => 4]);
        $assunto = $this->makeAssunto($user, [
            'teoria_finalizada' => true,
            'tipo'              => ['revisao'], // só revisão permitida
        ]);

        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 1);

        $tipos = collect($result['sessoes'])->pluck('tipo')->unique()->toArray();
        foreach ($tipos as $tipo) {
            $this->assertSame('revisao', $tipo);
        }
    }

    // -----------------------------------------------------------------------
    // Priorização por erro
    // -----------------------------------------------------------------------

    public function test_prioriza_assunto_com_maior_taxa_de_erros(): void
    {
        // Apenas 30min disponíveis — só uma sessão por dia
        $user = $this->makeUser(['segunda' => 0.5]);

        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $assuntoBom = Assunto::factory()->create([
            'materia_id'        => $materia->id,
            'teoria_finalizada' => true,
            'tipo'              => ['exercicio'],
        ]);
        Metrica::factory()->create([
            'assunto_id' => $assuntoBom->id,
            'acertos'    => 9,
            'erros'      => 1, // 10% de erro
        ]);

        $assuntoRuim = Assunto::factory()->create([
            'materia_id'        => $materia->id,
            'teoria_finalizada' => true,
            'tipo'              => ['exercicio'],
        ]);
        Metrica::factory()->create([
            'assunto_id' => $assuntoRuim->id,
            'acertos'    => 1,
            'erros'      => 9, // 90% de erro
        ]);

        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 1);

        $this->assertCount(1, $result['sessoes']);
        $this->assertSame($assuntoRuim->id, $result['sessoes'][0]['assunto_id']);
    }

    // -----------------------------------------------------------------------
    // Total e estrutura do retorno
    // -----------------------------------------------------------------------

    public function test_retorna_total_correto_de_sessoes_criadas(): void
    {
        $user = $this->makeUser(['segunda' => 1, 'terca' => 1]);
        $this->makeAssunto($user, ['teoria_finalizada' => false, 'tipo' => ['teoria']]);

        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 2);

        $this->assertSame($result['total'], count($result['sessoes']));
        $this->assertDatabaseCount('sessoes_estudo', $result['total']);
    }

    public function test_sessoes_criadas_estao_dentro_do_intervalo(): void
    {
        $user = $this->makeUser();
        $this->makeAssunto($user);

        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 5);

        foreach ($result['sessoes'] as $sessao) {
            $this->assertGreaterThanOrEqual('2025-01-06', $sessao['data']);
            $this->assertLessThanOrEqual('2025-01-10', Carbon::parse($sessao['data'])->toDateString());
        }
    }

    public function test_sessoes_criadas_com_finalizado_false(): void
    {
        $user = $this->makeUser();
        $this->makeAssunto($user);

        $inicio = Carbon::parse('2025-01-06');
        $result = $this->service->gerar($user, $inicio, 3);

        foreach ($result['sessoes'] as $sessao) {
            $this->assertFalse($sessao['finalizado']);
        }
    }

    // -----------------------------------------------------------------------
    // Início nulo usa hoje
    // -----------------------------------------------------------------------

    public function test_inicio_nulo_usa_data_de_hoje(): void
    {
        $user   = $this->makeUser();
        $result = $this->service->gerar($user, null, 7);

        $this->assertSame(Carbon::today()->toDateString(), $result['inicio']);
    }
}