<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Caderno;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadernoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_caderno_crud_is_scoped_to_authenticated_user_via_assunto_and_materia(): void
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

        $cadernoB = Caderno::create([
            'conteudo' => 'Conteúdo do B',
            'assunto_id' => $assuntoB->id,
        ]);

        $this->actingAs($userA);

        // Create (assunto_id precisa ser do usuário autenticado)
        $this->postJson('/api/cadernos', [
            'conteudo' => 'Tentativa',
            'assunto_id' => $assuntoB->id,
        ])->assertNotFound();

        $createResponse = $this->postJson('/api/cadernos', [
            'conteudo' => 'Meu conteúdo',
            'assunto_id' => $assuntoA->id,
        ]);

        $createResponse->assertCreated();
        $createdId = $createResponse->json('id');

        $this->assertDatabaseHas('cadernos', [
            'id' => $createdId,
            'conteudo' => 'Meu conteúdo',
            'assunto_id' => $assuntoA->id,
        ]);

        // Index retorna apenas cadernos do userA
        $indexResponse = $this->getJson('/api/cadernos');
        $indexResponse->assertOk();
        $this->assertCount(1, $indexResponse->json('data'));
        $this->assertEquals('Meu conteúdo', $indexResponse->json('data.0.conteudo'));

        // Show de caderno de outro usuário => 404
        $this->getJson("/api/cadernos/{$cadernoB->id}")->assertNotFound();

        // Update de caderno de outro usuário => 404
        $this->putJson("/api/cadernos/{$cadernoB->id}", [
            'conteudo' => 'Tentativa',
        ])->assertNotFound();

        // Delete de caderno de outro usuário => 404
        $this->deleteJson("/api/cadernos/{$cadernoB->id}")->assertNotFound();

        // Update próprio
        $this->putJson("/api/cadernos/{$createdId}", [
            'conteudo' => 'Conteúdo atualizado',
        ])->assertOk()->assertJson([
            'id' => $createdId,
            'conteudo' => 'Conteúdo atualizado',
            'assunto_id' => $assuntoA->id,
        ]);

        $this->assertDatabaseHas('cadernos', [
            'id' => $createdId,
            'conteudo' => 'Conteúdo atualizado',
            'assunto_id' => $assuntoA->id,
        ]);

        // Delete próprio
        $this->deleteJson("/api/cadernos/{$createdId}")->assertNoContent();
        $this->assertDatabaseMissing('cadernos', ['id' => $createdId]);
    }
}
