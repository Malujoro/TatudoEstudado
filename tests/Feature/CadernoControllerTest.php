<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Caderno;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadernoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createCaderno(User $user, array $attrs = []): Caderno
    {
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        return Caderno::factory()->create(array_merge([
            'assunto_id' => $assunto->id,
            'conteudo' => 'Conteúdo padrão',
        ], $attrs));
    }

    public function test_index_returns_only_users_cadernos(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->createCaderno($user);
        $this->createCaderno($user);
        $this->createCaderno($other);

        $response = $this->actingAs($user)->getJson(route('api.cadernos.index'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_show_returns_own_caderno(): void
    {
        $user = User::factory()->create();
        $caderno = $this->createCaderno($user, ['conteudo' => 'Meu conteúdo']);

        $response = $this->actingAs($user)->getJson(route('api.cadernos.show', $caderno));

        $response->assertOk();
        $response->assertJsonFragment(['conteudo' => 'Meu conteúdo']);
    }

    public function test_show_returns_404_for_other_users_caderno(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $caderno = $this->createCaderno($other);

        $response = $this->actingAs($user)->getJson(route('api.cadernos.show', $caderno));

        $response->assertNotFound();
    }

    public function test_store_creates_caderno_and_returns_201(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.cadernos.store'), [
            'assunto_id' => $assunto->id,
            'conteudo' => 'Anotações importantes',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('cadernos', [
            'assunto_id' => $assunto->id,
            'conteudo' => 'Anotações importantes',
        ]);
    }

    public function test_store_returns_404_when_assunto_belongs_to_other_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.cadernos.store'), [
            'assunto_id' => $assunto->id,
            'conteudo' => 'Tentativa',
        ]);

        $response->assertNotFound();
    }

    public function test_store_fails_without_conteudo(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.cadernos.store'), [
            'assunto_id' => $assunto->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['conteudo']);
    }

    public function test_update_modifies_caderno_successfully(): void
    {
        $user = User::factory()->create();
        $caderno = $this->createCaderno($user, ['conteudo' => 'Antigo']);

        $response = $this->actingAs($user)->putJson(route('api.cadernos.update', $caderno), [
            'conteudo' => 'Novo conteúdo',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['conteudo' => 'Novo conteúdo']);
    }

    public function test_update_returns_404_for_other_users_caderno(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $caderno = $this->createCaderno($other);

        $response = $this->actingAs($user)->putJson(route('api.cadernos.update', $caderno), [
            'conteudo' => 'Invasão',
        ]);

        $response->assertNotFound();
    }

    public function test_destroy_deletes_caderno_and_returns_204(): void
    {
        $user = User::factory()->create();
        $caderno = $this->createCaderno($user);

        $response = $this->actingAs($user)->deleteJson(route('api.cadernos.destroy', $caderno));

        $response->assertNoContent();
        $this->assertDatabaseMissing('cadernos', ['id' => $caderno->id]);
    }

    public function test_destroy_returns_404_for_other_users_caderno(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $caderno = $this->createCaderno($other);

        $response = $this->actingAs($user)->deleteJson(route('api.cadernos.destroy', $caderno));

        $response->assertNotFound();
        $this->assertDatabaseHas('cadernos', ['id' => $caderno->id]);
    }
}
