<?php

namespace Tests\Feature;

use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para MateriaController
 *
 * Cenários cobertos:
 * - index: lista somente as matérias do usuário autenticado (paginadas)
 * - index: per_page customizado e fallback para 15 em valores inválidos (<= 0)
 * - index: usuário não autenticado recebe 401/302
 * - show: retorna matéria própria com os campos corretos
 * - show: retorna 404 para matéria de outro usuário
 * - store: cria matéria e retorna 201 com payload correto
 * - store: falha de validação quando nome ausente
 * - store: associa automaticamente o user_id autenticado
 * - update: atualiza matéria própria com sucesso
 * - update: retorna 404 ao tentar atualizar matéria de outro usuário
 * - destroy: deleta matéria própria e retorna 204
 * - destroy: retorna 404 ao tentar deletar matéria de outro usuário
 */
class MateriaControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // index
    // -----------------------------------------------------------------------

    public function test_index_returns_only_authenticated_user_materias(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Materia::factory()->count(3)->create(['user_id' => $user->id]);
        Materia::factory()->count(2)->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->getJson(route('api.materias.index'));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_index_uses_custom_per_page(): void
    {
        $user = User::factory()->create();
        Materia::factory()->count(10)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('api.materias.index', ['per_page' => 4]));

        $response->assertOk();
        $response->assertJsonCount(4, 'data');
    }

    public function test_index_falls_back_to_15_for_invalid_per_page(): void
    {
        $user = User::factory()->create();
        Materia::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('api.materias.index', ['per_page' => -5]));

        $response->assertOk();
        // per_page voltou para 15, total < 15 então retorna todos
        $response->assertJsonCount(5, 'data');
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson(route('api.materias.index'));

        $response->assertUnauthorized();
    }

    // -----------------------------------------------------------------------
    // show
    // -----------------------------------------------------------------------

    public function test_show_returns_own_materia(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('api.materias.show', $materia));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $materia->id, 'nome' => $materia->nome]);
    }

    public function test_show_returns_404_for_other_users_materia(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->getJson(route('api.materias.show', $materia));

        $response->assertNotFound();
    }

    public function test_show_returns_correct_fields(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('api.materias.show', $materia));

        $response->assertJsonStructure(['id', 'nome', 'user_id', 'created_at', 'updated_at']);
        $response->assertJsonMissing(['password']);
    }

    // -----------------------------------------------------------------------
    // store
    // -----------------------------------------------------------------------

    public function test_store_creates_materia_and_returns_201(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.materias.store'), [
            'nome' => 'Matemática',
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['nome' => 'Matemática']);
        $this->assertDatabaseHas('materias', ['nome' => 'Matemática', 'user_id' => $user->id]);
    }

    public function test_store_assigns_authenticated_user_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.materias.store'), [
            'nome' => 'Física',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('materias', ['nome' => 'Física', 'user_id' => $user->id]);
    }

    public function test_store_fails_when_nome_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.materias.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['nome']);
    }

    // -----------------------------------------------------------------------
    // update
    // -----------------------------------------------------------------------

    public function test_update_changes_materia_and_returns_200(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id, 'nome' => 'Antigo']);

        $response = $this->actingAs($user)->putJson(route('api.materias.update', $materia), [
            'nome' => 'Novo Nome',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['nome' => 'Novo Nome']);
        $this->assertDatabaseHas('materias', ['id' => $materia->id, 'nome' => 'Novo Nome']);
    }

    public function test_update_returns_404_for_other_users_materia(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->putJson(route('api.materias.update', $materia), [
            'nome' => 'Tentativa',
        ]);

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // destroy
    // -----------------------------------------------------------------------

    public function test_destroy_deletes_materia_and_returns_204(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson(route('api.materias.destroy', $materia));

        $response->assertNoContent();
        $this->assertDatabaseMissing('materias', ['id' => $materia->id]);
    }

    public function test_destroy_returns_404_for_other_users_materia(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->deleteJson(route('api.materias.destroy', $materia));

        $response->assertNotFound();
        $this->assertDatabaseHas('materias', ['id' => $materia->id]);
    }
}
