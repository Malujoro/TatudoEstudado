<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\SessaoEstudo;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessaoEstudoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_sessao_estudo_crud_is_scoped_to_authenticated_user_via_assunto_and_materia(): void
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

        $sessaoB = SessaoEstudo::create([
            'data' => '2026-05-01',
            'tipo' => 'revisao',
            'horas' => 1.0,
            'finalizado' => true,
            'assunto_id' => $assuntoB->id,
        ]);

        $this->actingAs($userA);

        // Create (assunto_id precisa ser do usuário autenticado)
        $this->postJson('/api/sessoes-estudo', [
            'data' => '2026-05-02',
            'tipo' => 'estudo',
            'horas' => 2,
            'finalizado' => false,
            'assunto_id' => $assuntoB->id,
        ])->assertNotFound();

        $createResponse = $this->postJson('/api/sessoes-estudo', [
            'data' => '2026-05-02',
            'tipo' => 'estudo',
            'horas' => 2,
            'finalizado' => false,
            'assunto_id' => $assuntoA->id,
        ]);

        $createResponse->assertCreated();
        $createdId = $createResponse->json('id');

        $this->assertDatabaseHas('sessoes_estudo', [
            'id' => $createdId,
            'assunto_id' => $assuntoA->id,
            'tipo' => 'estudo',
        ]);

        // Index retorna apenas sessões do userA
        $indexResponse = $this->getJson('/api/sessoes-estudo');
        $indexResponse->assertOk();
        $this->assertCount(1, $indexResponse->json('data'));
        $this->assertEquals('estudo', $indexResponse->json('data.0.tipo'));

        // Show de sessão de outro usuário => 404
        $this->getJson("/api/sessoes-estudo/{$sessaoB->id}")->assertNotFound();

        // Update de sessão de outro usuário => 404
        $this->putJson("/api/sessoes-estudo/{$sessaoB->id}", [
            'finalizado' => false,
        ])->assertNotFound();

        // Delete de sessão de outro usuário => 404
        $this->deleteJson("/api/sessoes-estudo/{$sessaoB->id}")->assertNotFound();

        // Update própria
        $this->putJson("/api/sessoes-estudo/{$createdId}", [
            'finalizado' => true,
            'horas' => 3.5,
        ])->assertOk()->assertJson([
            'id' => $createdId,
            'finalizado' => true,
            'assunto_id' => $assuntoA->id,
        ]);

        $this->assertDatabaseHas('sessoes_estudo', [
            'id' => $createdId,
            'finalizado' => 1,
        ]);

        // Delete própria
        $this->deleteJson("/api/sessoes-estudo/{$createdId}")->assertNoContent();
        $this->assertDatabaseMissing('sessoes_estudo', ['id' => $createdId]);
    }
}
