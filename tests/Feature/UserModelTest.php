<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\SessaoEstudo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // Helpers
    private function criarSessaoFinalizada(User $user, string $data): void
    {
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
            'data' => $data,
            'finalizado' => true,
            'tipo' => 'teoria',
            'horas' => 1.0,
        ]);
    }

    // materias()
    public function test_materias_retorna_materias_do_usuario(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Materia::factory()->count(3)->create(['user_id' => $user->id]);
        Materia::factory()->count(2)->create(['user_id' => $other->id]);

        $this->assertCount(3, $user->materias);
    }

    // obterSequenciaEstudo() — sem sessões
    public function test_sequencia_zero_sem_sessoes_finalizadas(): void
    {
        $user = User::factory()->create();

        $this->assertSame(0, $user->obterSequenciaEstudo());
    }

    public function test_sequencia_zero_quando_streak_quebrado(): void
    {
        $user = User::factory()->create();

        // Última sessão foi há dois dias — streak quebrado
        $this->criarSessaoFinalizada($user, Carbon::today()->subDays(2)->toDateString());

        $this->assertSame(0, $user->obterSequenciaEstudo());
    }

    // obterSequenciaEstudo() — streak ativo hoje
    public function test_sequencia_um_quando_estudou_apenas_hoje(): void
    {
        $user = User::factory()->create();

        $this->criarSessaoFinalizada($user, Carbon::today()->toDateString());

        $this->assertSame(1, $user->obterSequenciaEstudo());
    }

    public function test_sequencia_conta_dias_consecutivos_a_partir_de_hoje(): void
    {
        $user = User::factory()->create();

        $this->criarSessaoFinalizada($user, Carbon::today()->toDateString());
        $this->criarSessaoFinalizada($user, Carbon::today()->subDay()->toDateString());
        $this->criarSessaoFinalizada($user, Carbon::today()->subDays(2)->toDateString());

        $this->assertSame(3, $user->obterSequenciaEstudo());
    }

    // obterSequenciaEstudo() — streak ativo ontem (sem sessão hoje)
    public function test_sequencia_um_quando_estudou_apenas_ontem(): void
    {
        $user = User::factory()->create();

        $this->criarSessaoFinalizada($user, Carbon::today()->subDay()->toDateString());

        $this->assertSame(1, $user->obterSequenciaEstudo());
    }

    public function test_sequencia_conta_dias_consecutivos_a_partir_de_ontem(): void
    {
        $user = User::factory()->create();

        $this->criarSessaoFinalizada($user, Carbon::today()->subDay()->toDateString());
        $this->criarSessaoFinalizada($user, Carbon::today()->subDays(2)->toDateString());
        $this->criarSessaoFinalizada($user, Carbon::today()->subDays(3)->toDateString());

        $this->assertSame(3, $user->obterSequenciaEstudo());
    }

    // obterSequenciaEstudo() — não conta sessões de outro usuário
    public function test_sequencia_ignora_sessoes_de_outro_usuario(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->criarSessaoFinalizada($other, Carbon::today()->toDateString());

        $this->assertSame(0, $user->obterSequenciaEstudo());
    }

    // getSequenciaEstudoAttribute()
    public function test_accessor_sequencia_estudo_delega_para_obter(): void
    {
        $user = User::factory()->create();

        $this->criarSessaoFinalizada($user, Carbon::today()->toDateString());

        // Acessado via accessor mágico
        $this->assertSame(1, $user->sequencia_estudo);
    }
}
