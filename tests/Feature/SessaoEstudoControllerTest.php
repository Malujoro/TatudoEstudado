<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\Metrica;
use App\Models\SessaoEstudo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para SessaoEstudoController
 *
 * Cenários cobertos:
 * - index: lista apenas sessões do usuário, com paginação
 * - show: retorna sessão própria; 404 para sessão de outro usuário
 * - store: cria sessão vinculada a assunto do usuário; retorna 201
 * - store: 404 quando assunto não pertence ao usuário
 * - store: finalizado padrão false quando não informado
 * - update: atualiza sessão própria; 404 para sessão alheia
 * - destroy: deleta sessão própria (204); 404 para sessão alheia
 * - finalizar (tipo=teoria): marca sessão como finalizada e teoria_finalizada=true no assunto
 * - finalizar (tipo=exercicio): atualiza métricas com acertos/erros corretos
 * - finalizar (tipo=exercicio): retorna 422 quando questoes/acertos não informados
 * - finalizar (tipo=exercicio): retorna 422 quando acertos > questoes
 * - finalizar: sessão já finalizada retorna 200 sem alterar estado
 * - finalizar: 404 para sessão de outro usuário
 */
class SessaoEstudoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createSessao(User $user, array $attrs = []): SessaoEstudo
    {
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        return SessaoEstudo::factory()->create(array_merge([
            'assunto_id' => $assunto->id,
            'tipo' => 'teoria',
            'finalizado' => false,
            'data' => now()->toDateString(),
            'horas' => 1.5,
        ], $attrs));
    }

    // -----------------------------------------------------------------------
    // index
    // -----------------------------------------------------------------------

    public function test_index_returns_only_users_sessoes(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->createSessao($user);
        $this->createSessao($user);
        $this->createSessao($other);

        $response = $this->actingAs($user)->getJson(route('api.sessoes-estudo.index'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_paginates_correctly(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 5; $i++) {
            $this->createSessao($user);
        }

        $response = $this->actingAs($user)->getJson(route('api.sessoes-estudo.index', ['per_page' => 2]));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson(route('api.sessoes-estudo.index'));

        $response->assertUnauthorized();
    }

    // -----------------------------------------------------------------------
    // show
    // -----------------------------------------------------------------------

    public function test_show_returns_own_sessao(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user);

        $response = $this->actingAs($user)->getJson(route('api.sessoes-estudo.show', $sessao));

        $response->assertOk();
        $response->assertJsonStructure(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at']);
        $response->assertJsonFragment(['id' => $sessao->id]);
    }

    public function test_show_returns_404_for_other_users_sessao(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $sessao = $this->createSessao($other);

        $response = $this->actingAs($user)->getJson(route('api.sessoes-estudo.show', $sessao));

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // store
    // -----------------------------------------------------------------------

    public function test_store_creates_sessao_and_returns_201(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.store'), [
            'assunto_id' => $assunto->id,
            'tipo' => 'teoria',
            'data' => '2025-01-10',
            'horas' => 2.0,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('sessoes_estudo', ['assunto_id' => $assunto->id, 'tipo' => 'teoria']);
    }

    public function test_store_defaults_finalizado_to_false(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.store'), [
            'assunto_id' => $assunto->id,
            'tipo' => 'teoria',
            'data' => '2025-01-10',
            'horas' => 1.0,
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['finalizado' => false]);
    }

    public function test_store_returns_404_when_assunto_belongs_to_other_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.store'), [
            'assunto_id' => $assunto->id,
            'tipo' => 'teoria',
            'data' => '2025-01-10',
            'horas' => 1.0,
        ]);

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // update
    // -----------------------------------------------------------------------

    public function test_update_modifies_sessao_successfully(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['horas' => 1.0]);

        $response = $this->actingAs($user)->putJson(route('api.sessoes-estudo.update', $sessao), [
            'horas' => 3.5,
            'tipo' => 'revisao',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['horas' => 3.5, 'tipo' => 'revisao']);
    }

    public function test_update_returns_404_for_other_users_sessao(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $sessao = $this->createSessao($other);

        $response = $this->actingAs($user)->putJson(route('api.sessoes-estudo.update', $sessao), [
            'horas' => 2.0,
        ]);

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // destroy
    // -----------------------------------------------------------------------

    public function test_destroy_deletes_sessao_and_returns_204(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user);

        $response = $this->actingAs($user)->deleteJson(route('api.sessoes-estudo.destroy', $sessao));

        $response->assertNoContent();
        $this->assertDatabaseMissing('sessoes_estudo', ['id' => $sessao->id]);
    }

    public function test_destroy_returns_404_for_other_users_sessao(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $sessao = $this->createSessao($other);

        $response = $this->actingAs($user)->deleteJson(route('api.sessoes-estudo.destroy', $sessao));

        $response->assertNotFound();
        $this->assertDatabaseHas('sessoes_estudo', ['id' => $sessao->id]);
    }

    // -----------------------------------------------------------------------
    // finalizar — tipo=teoria
    // -----------------------------------------------------------------------

    public function test_finalizar_teoria_marks_sessao_and_assunto(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['tipo' => 'teoria', 'finalizado' => false]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao));

        $response->assertOk();
        $response->assertJsonFragment(['finalizado' => true]);

        $this->assertDatabaseHas('sessoes_estudo', ['id' => $sessao->id, 'finalizado' => true]);
        $this->assertDatabaseHas('assuntos', [
            'id' => $sessao->assunto_id,
            'teoria_finalizada' => true,
        ]);
    }

    // -----------------------------------------------------------------------
    // finalizar — tipo=exercicio
    // -----------------------------------------------------------------------

    public function test_finalizar_exercicio_creates_and_updates_metrica(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['tipo' => 'exercicio', 'finalizado' => false]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao), [
            'questoes' => 10,
            'acertos' => 7,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('metricas', [
            'assunto_id' => $sessao->assunto_id,
            'acertos' => 7,
            'erros' => 3,
        ]);
    }

    public function test_finalizar_exercicio_accumulates_existing_metrica(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        Metrica::factory()->create([
            'assunto_id' => $assunto->id,
            'acertos' => 5,
            'erros' => 2,
        ]);

        $sessao = SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
            'tipo' => 'exercicio',
            'finalizado' => false,
            'data' => now()->toDateString(),
            'horas' => 1,
        ]);

        $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao), [
            'questoes' => 10,
            'acertos' => 4,
        ]);

        $this->assertDatabaseHas('metricas', [
            'assunto_id' => $assunto->id,
            'acertos' => 9,   // 5 + 4
            'erros' => 8,   // 2 + 6
        ]);
    }

    public function test_finalizar_exercicio_returns_422_without_questoes_acertos(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['tipo' => 'exercicio', 'finalizado' => false]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao));

        $response->assertUnprocessable();
        $response->assertJsonFragment(['message' => 'Informe a quantidade de questões e acertos.']);
    }

    public function test_finalizar_exercicio_returns_422_when_acertos_greater_than_questoes(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['tipo' => 'exercicio', 'finalizado' => false]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao), [
            'questoes' => 5,
            'acertos' => 10,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonFragment(['message' => 'Acertos não podem ser maiores que questões.']);
    }

    // -----------------------------------------------------------------------
    // finalizar — edge cases
    // -----------------------------------------------------------------------

    public function test_finalizar_already_finalized_session_returns_200_without_changes(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['tipo' => 'teoria', 'finalizado' => true]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao));

        $response->assertOk();
        $response->assertJsonFragment(['finalizado' => true]);
        // assunto não deve ser alterado novamente — apenas 1 registro de sessao
        $this->assertDatabaseCount('sessoes_estudo', 1);
    }

    public function test_finalizar_returns_404_for_other_users_sessao(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $sessao = $this->createSessao($other, ['tipo' => 'teoria']);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao));

        $response->assertNotFound();
    }

    public function test_finalizar_exercicio_with_zero_acertos_is_valid(): void
    {
        $user = User::factory()->create();
        $sessao = $this->createSessao($user, ['tipo' => 'exercicio', 'finalizado' => false]);

        $response = $this->actingAs($user)->postJson(route('api.sessoes-estudo.finalizar', $sessao), [
            'questoes' => 5,
            'acertos' => 0,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('metricas', [
            'assunto_id' => $sessao->assunto_id,
            'acertos' => 0,
            'erros' => 5,
        ]);
    }
}
