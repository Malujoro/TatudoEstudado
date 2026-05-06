<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssuntoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_assunto_crud_is_scoped_to_authenticated_user_via_materia(): void
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

        $assuntoB = Assunto::create([
            'nome' => 'Assunto do B',
            'materia_id' => $materiaB->id,
        ]);

        $this->actingAs($userA);

        // Create (materia_id precisa ser do usuário autenticado)
        $this->postJson('/api/assuntos', [
            'nome' => 'Álgebra',
            'materia_id' => $materiaB->id,
        ])->assertNotFound();

        $createResponse = $this->postJson('/api/assuntos', [
            'nome' => 'Álgebra',
            'materia_id' => $materiaA->id,
        ]);

        $createResponse->assertCreated();
        $createdId = $createResponse->json('id');

        $this->assertDatabaseHas('assuntos', [
            'id' => $createdId,
            'nome' => 'Álgebra',
            'materia_id' => $materiaA->id,
        ]);

        // Index retorna apenas assuntos do userA (via matéria do userA)
        $indexResponse = $this->getJson('/api/assuntos');
        $indexResponse->assertOk();
        $this->assertCount(1, $indexResponse->json('data'));
        $this->assertEquals('Álgebra', $indexResponse->json('data.0.nome'));

        // Show de assunto de outro usuário => 404
        $this->getJson("/api/assuntos/{$assuntoB->id}")->assertNotFound();

        // Update de assunto de outro usuário => 404
        $this->putJson("/api/assuntos/{$assuntoB->id}", [
            'nome' => 'Tentativa',
        ])->assertNotFound();

        // Delete de assunto de outro usuário => 404
        $this->deleteJson("/api/assuntos/{$assuntoB->id}")->assertNotFound();

        // Update próprio
        $this->putJson("/api/assuntos/{$createdId}", [
            'nome' => 'Álgebra II',
        ])->assertOk()->assertJson([
            'id' => $createdId,
            'nome' => 'Álgebra II',
            'materia_id' => $materiaA->id,
        ]);

        $this->assertDatabaseHas('assuntos', [
            'id' => $createdId,
            'nome' => 'Álgebra II',
            'materia_id' => $materiaA->id,
        ]);

        // Delete próprio
        $this->deleteJson("/api/assuntos/{$createdId}")->assertNoContent();
        $this->assertDatabaseMissing('assuntos', ['id' => $createdId]);
    }
}
