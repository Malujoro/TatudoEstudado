<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\Metrica;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createMetrica(User $user, array $attrs = []): Metrica
    {
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        return Metrica::factory()->create(array_merge([
            'assunto_id' => $assunto->id,
            'acertos' => 0,
            'erros' => 0,
        ], $attrs));
    }

    public function test_index_returns_only_users_metricas(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->createMetrica($user);
        $this->createMetrica($user);
        $this->createMetrica($other);

        $response = $this->actingAs($user)->getJson(route('api.metricas.index'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_index_paginates_with_custom_per_page(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 6; $i++) {
            $this->createMetrica($user);
        }

        $response = $this->actingAs($user)->getJson(route('api.metricas.index', ['per_page' => 2]));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_show_returns_own_metrica(): void
    {
        $user = User::factory()->create();
        $metrica = $this->createMetrica($user, ['acertos' => 10, 'erros' => 3]);

        $response = $this->actingAs($user)->getJson(route('api.metricas.show', $metrica));

        $response->assertOk();
        $response->assertJsonFragment(['acertos' => 10, 'erros' => 3]);
    }

    public function test_show_returns_404_for_other_users_metrica(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $metrica = $this->createMetrica($other);

        $response = $this->actingAs($user)->getJson(route('api.metricas.show', $metrica));

        $response->assertNotFound();
    }

    public function test_store_creates_metrica_with_default_zeros(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.metricas.store'), [
            'assunto_id' => $assunto->id,
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['acertos' => 0, 'erros' => 0]);
    }

    public function test_store_creates_metrica_with_explicit_values(): void
    {
        $user = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $user->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.metricas.store'), [
            'assunto_id' => $assunto->id,
            'acertos' => 8,
            'erros' => 2,
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['acertos' => 8, 'erros' => 2]);
    }

    public function test_store_returns_404_when_assunto_belongs_to_other_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $materia = Materia::factory()->create(['user_id' => $other->id]);
        $assunto = Assunto::factory()->create(['materia_id' => $materia->id]);

        $response = $this->actingAs($user)->postJson(route('api.metricas.store'), [
            'assunto_id' => $assunto->id,
        ]);

        $response->assertNotFound();
    }

    public function test_store_fails_without_assunto_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.metricas.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['assunto_id']);
    }

    public function test_update_modifies_metrica(): void
    {
        $user = User::factory()->create();
        $metrica = $this->createMetrica($user, ['acertos' => 2, 'erros' => 1]);

        $response = $this->actingAs($user)->putJson(route('api.metricas.update', $metrica), [
            'acertos' => 15,
            'erros' => 5,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['acertos' => 15, 'erros' => 5]);
    }

    public function test_update_returns_404_for_other_users_metrica(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $metrica = $this->createMetrica($other);

        $response = $this->actingAs($user)->putJson(route('api.metricas.update', $metrica), [
            'acertos' => 99,
        ]);

        $response->assertNotFound();
    }

    public function test_destroy_deletes_metrica_and_returns_204(): void
    {
        $user = User::factory()->create();
        $metrica = $this->createMetrica($user);

        $response = $this->actingAs($user)->deleteJson(route('api.metricas.destroy', $metrica));

        $response->assertNoContent();
        $this->assertDatabaseMissing('metricas', ['id' => $metrica->id]);
    }

    public function test_destroy_returns_404_for_other_users_metrica(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $metrica = $this->createMetrica($other);

        $response = $this->actingAs($user)->deleteJson(route('api.metricas.destroy', $metrica));

        $response->assertNotFound();
        $this->assertDatabaseHas('metricas', ['id' => $metrica->id]);
    }
}
