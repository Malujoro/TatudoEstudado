<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para AssuntoController
 *
 * Cenários cobertos:
 * - index: lista apenas assuntos cujas matérias pertencem ao usuário autenticado
 * - index: paginação customizada e fallback para per_page inválido
 * - index: requer autenticação
 * - show: retorna assunto próprio com campos corretos
 * - show: 404 para assunto de outro usuário
 * - store: cria assunto vinculado a matéria do usuário; retorna 201
 * - store: 404 quando materia_id não pertence ao usuário
 * - store: falha de validação quando campos obrigatórios estão ausentes
 * - store: teoria_finalizada padrão é false quando não informado
 * - update: atualiza assunto próprio com sucesso
 * - update: 404 ao atualizar assunto de outro usuário
 * - destroy: deleta assunto próprio e retorna 204
 * - destroy: 404 ao deletar assunto de outro usuário
 */
class AssuntoControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // index
    // -----------------------------------------------------------------------

    public function test_index_returns_only_users_assuntos(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $materiaOwn = Materia::factory()->create(['user_id' => $user->id]);
        $materiaOther = Materia::factory()->create(['user_id' => $other->id]);

        Assunto::factory()->count(3)->create(['materia_id' => $materiaOwn->id]);
        Assunto::factory()->count(2)->create(['materia_id' => $materiaOther->id]);

        $response = $this->actingAs($user)->getJson(route('api.assuntos.index'));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_index_paginates_with_custom_per_page(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        Assunto::factory()->count(10)->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->getJson(route('api.assuntos.index', ['per_page' => 3]));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_index_falls_back_to_15_for_zero_per_page(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        Assunto::factory()->count(5)->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->getJson(route('api.assuntos.index', ['per_page' => 0]));

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson(route('api.assuntos.index'));

        $response->assertUnauthorized();
    }

    // -----------------------------------------------------------------------
    // show
    // -----------------------------------------------------------------------

    public function test_show_returns_own_assunto(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->getJson(route('api.assuntos.show', $assunto));

        $response->assertOk();
        $response->assertJsonStructure(['id', 'nome', 'teoria_finalizada', 'tipo', 'materia_id', 'created_at', 'updated_at']);
        $response->assertJsonFragment(['id' => $assunto->id]);
    }

    public function test_show_returns_404_for_other_users_assunto(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->getJson(route('api.assuntos.show', $assunto));

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // store
    // -----------------------------------------------------------------------

    public function test_store_creates_assunto_and_returns_201(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('api.assuntos.store'), [
            'nome' => 'Frações',
            'materia_id' => $materia->id,
            'tipo' => ['teoria', 'exercicio'],
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['nome' => 'Frações', 'materia_id' => $materia->id]);
        $this->assertDatabaseHas('assuntos', ['nome' => 'Frações', 'materia_id' => $materia->id]);
    }

    public function test_store_defaults_teoria_finalizada_to_false(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('api.assuntos.store'), [
            'nome' => 'Integral',
            'materia_id' => $materia->id,
            'tipo' => ['teoria'],
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['teoria_finalizada' => false]);
    }

    public function test_store_returns_404_when_materia_belongs_to_other_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->postJson(route('api.assuntos.store'), [
            'nome' => 'Tentativa',
            'materia_id' => $materia->id,
            'tipo' => ['teoria'],
        ]);

        $response->assertNotFound();
        $this->assertDatabaseMissing('assuntos', ['nome' => 'Tentativa']);
    }

    public function test_store_fails_when_nome_is_missing(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('api.assuntos.store'), [
            'materia_id' => $materia->id,
            'tipo' => ['teoria'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['nome']);
    }

    public function test_store_fails_when_materia_id_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.assuntos.store'), [
            'nome' => 'Sem Matéria',
            'tipo' => ['teoria'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['materia_id']);
    }

    // -----------------------------------------------------------------------
    // update
    // -----------------------------------------------------------------------

    public function test_update_modifies_assunto_successfully(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create([
            'materia_id' => $materia->id,
            'nome' => 'Antigo',
            'teoria_finalizada' => false,
        ]);

        $response = $this->actingAs($user)->putJson(route('api.assuntos.update', $assunto), [
            'nome' => 'Novo',
            'teoria_finalizada' => true,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['nome' => 'Novo', 'teoria_finalizada' => true]);
        $this->assertDatabaseHas('assuntos', ['id' => $assunto->id, 'nome' => 'Novo']);
    }

    public function test_update_returns_404_for_other_users_assunto(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->putJson(route('api.assuntos.update', $assunto), [
            'nome' => 'Invasão',
        ]);

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // destroy
    // -----------------------------------------------------------------------

    public function test_destroy_deletes_assunto_and_returns_204(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->deleteJson(route('api.assuntos.destroy', $assunto));

        $response->assertNoContent();
        $this->assertDatabaseMissing('assuntos', ['id' => $assunto->id]);
    }

    public function test_destroy_returns_404_for_other_users_assunto(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->deleteJson(route('api.assuntos.destroy', $assunto));

        $response->assertNotFound();
        $this->assertDatabaseHas('assuntos', ['id' => $assunto->id]);
    }
}
