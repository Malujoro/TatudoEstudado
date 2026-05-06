<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\Metrica;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrica_crud_is_scoped_to_authenticated_user_via_assunto_and_materia(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $materiaA = Materia::create([
            'nome' => 'Matéria do A',
            'user_id' => $userA->id,
        ]);

        $materiaB = Materia::create([
            'nome' => 'Matéria do B',
            'user_id' => $userB->id,
        ]);

        $assuntoA = Assunto::create([
            'nome' => 'Assunto do A',
            'materia_id' => $materiaA->id,
        ]);

        $assuntoB = Assunto::create([
            'nome' => 'Assunto do B',
            'materia_id' => $materiaB->id,
        ]);

        $metricaB = Metrica::create([
            'acertos' => 3,
            'erros' => 1,
            'assunto_id' => $assuntoB->id,
        ]);

        $this->actingAs($userA);

        // Create (assunto_id precisa ser do usuário autenticado)
        $this->postJson('/api/metricas', [
            'assunto_id' => $assuntoB->id,
            'acertos' => 10,
            'erros' => 0,
        ])->assertNotFound();

        $createResponse = $this->postJson('/api/metricas', [
            'assunto_id' => $assuntoA->id,
            'acertos' => 10,
            'erros' => 2,
        ]);

        $createResponse->assertCreated();
        $createdId = $createResponse->json('id');

        $this->assertDatabaseHas('metricas', [
            'id' => $createdId,
            'assunto_id' => $assuntoA->id,
            'acertos' => 10,
            'erros' => 2,
        ]);

        // Index retorna apenas métricas do userA
        $indexResponse = $this->getJson('/api/metricas');
        $indexResponse->assertOk();
        $this->assertCount(1, $indexResponse->json('data'));
        $this->assertEquals(10, $indexResponse->json('data.0.acertos'));

        // Show de métrica de outro usuário => 404
        $this->getJson("/api/metricas/{$metricaB->id}")->assertNotFound();

        // Update de métrica de outro usuário => 404
        $this->putJson("/api/metricas/{$metricaB->id}", [
            'acertos' => 999,
        ])->assertNotFound();

        // Delete de métrica de outro usuário => 404
        $this->deleteJson("/api/metricas/{$metricaB->id}")->assertNotFound();

        // Update própria
        $this->putJson("/api/metricas/{$createdId}", [
            'acertos' => 11,
        ])->assertOk()->assertJson([
            'id' => $createdId,
            'acertos' => 11,
            'erros' => 2,
            'assunto_id' => $assuntoA->id,
        ]);

        $this->assertDatabaseHas('metricas', [
            'id' => $createdId,
            'acertos' => 11,
            'erros' => 2,
            'assunto_id' => $assuntoA->id,
        ]);

        // Delete própria
        $this->deleteJson("/api/metricas/{$createdId}")->assertNoContent();
        $this->assertDatabaseMissing('metricas', ['id' => $createdId]);
    }

    public function test_cannot_create_two_metricas_for_same_assunto(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $materia = Materia::create([
            'nome' => 'Matéria',
            'user_id' => $user->id,
        ]);

        $assunto = Assunto::create([
            'nome' => 'Assunto',
            'materia_id' => $materia->id,
        ]);

        $this->postJson('/api/metricas', [
            'assunto_id' => $assunto->id,
            'acertos' => 1,
        ])->assertCreated();

        $this->postJson('/api/metricas', [
            'assunto_id' => $assunto->id,
            'acertos' => 2,
        ])->assertStatus(422);
    }
}
