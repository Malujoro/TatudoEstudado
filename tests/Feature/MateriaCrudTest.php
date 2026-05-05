<?php

namespace Tests\Feature;

use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class MateriaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_materia_crud_is_scoped_to_authenticated_user(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $materiaB = Materia::create([
            'nome' => 'Matéria do B',
            'user_id' => $userB->id,
        ]);

        $this->actingAs($userA);

        // Create (user_id não vem do cliente)
        $createResponse = $this->postJson('/api/materias', [
            'nome' => 'Matemática',
            'user_id' => $userB->id,
        ]);

        $createResponse->assertCreated();
        $createdId = $createResponse->json('id');

        $this->assertDatabaseHas('materias', [
            'id' => $createdId,
            'nome' => 'Matemática',
            'user_id' => $userA->id,
        ]);

        // Index retorna apenas do userA
        $indexResponse = $this->getJson('/api/materias');
        $indexResponse->assertOk();
        $this->assertCount(1, $indexResponse->json('data'));
        $this->assertEquals('Matemática', $indexResponse->json('data.0.nome'));

        // Show de matéria de outro usuário => 404
        $this->getJson("/api/materias/{$materiaB->id}")->assertNotFound();

        // Update de matéria de outro usuário => 404
        $this->putJson("/api/materias/{$materiaB->id}", [
            'nome' => 'Tentativa',
        ])->assertNotFound();

        // Delete de matéria de outro usuário => 404
        $this->deleteJson("/api/materias/{$materiaB->id}")->assertNotFound();

        // Update própria
        $this->putJson("/api/materias/{$createdId}", [
            'nome' => 'Matemática II',
        ])->assertOk()->assertJson([
            'id' => $createdId,
            'nome' => 'Matemática II',
        ]);

        $this->assertDatabaseHas('materias', [
            'id' => $createdId,
            'nome' => 'Matemática II',
            'user_id' => $userA->id,
        ]);

        // Delete própria
        $this->deleteJson("/api/materias/{$createdId}")->assertNoContent();
        $this->assertDatabaseMissing('materias', ['id' => $createdId]);
    }
}
