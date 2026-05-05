<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_crud_endpoints_work_for_authenticated_user(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $authUser = User::factory()->create();
        $this->actingAs($authUser);

        // Create
        $createResponse = $this->postJson('/api/users', [
            'name' => 'Fulano da Silva',
            'email' => 'fulano@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'horas_por_dia' => 2.5,
            'role' => 'user',
        ]);

        $createResponse->assertCreated();
        $createdId = $createResponse->json('id');

        $this->assertDatabaseHas('users', [
            'id' => $createdId,
            'email' => 'fulano@example.com',
            'role' => 'user',
        ]);

        // Index
        $indexResponse = $this->getJson('/api/users');
        $indexResponse->assertOk();
        $indexResponse->assertJsonStructure(['data', 'links', 'meta']);

        // Show
        $showResponse = $this->getJson("/api/users/{$createdId}");
        $showResponse->assertOk();
        $showResponse->assertJson([
            'id' => $createdId,
            'email' => 'fulano@example.com',
        ]);

        // Update (without password)
        $updateResponse = $this->putJson("/api/users/{$createdId}", [
            'name' => 'Fulano Atualizado',
            'horas_por_dia' => 4,
        ]);
        $updateResponse->assertOk();
        $updateResponse->assertJson([
            'id' => $createdId,
            'name' => 'Fulano Atualizado',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $createdId,
            'name' => 'Fulano Atualizado',
        ]);

        // Delete
        $deleteResponse = $this->deleteJson("/api/users/{$createdId}");
        $deleteResponse->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'id' => $createdId,
        ]);
    }
}
