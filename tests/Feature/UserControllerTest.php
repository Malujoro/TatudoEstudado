<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para UserController
 *
 * Cenários cobertos:
 * - index: lista usuários paginados com campos corretos (sem password)
 * - index: paginação customizada
 * - show: retorna usuário por ID
 * - store: cria usuário com role e horario_semanal padrão; retorna 201
 * - store: cria usuário com role explícito
 * - store: falha quando email está duplicado
 * - store: falha quando campos obrigatórios ausentes
 * - update: atualiza campos do usuário
 * - update: não altera a senha quando campo password é vazio/nulo
 * - destroy: deleta usuário e retorna 204
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson(route('api.users.index'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(6, $response->json('total'));
    }

    public function test_index_does_not_expose_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->getJson(route('api.users.index'));

        $response->assertOk();
        foreach ($response->json('data') as $u) {
            $this->assertArrayNotHasKey('password', $u);
        }
    }

    public function test_index_custom_per_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(10)->create();

        $response = $this->actingAs($admin)->getJson(route('api.users.index', ['per_page' => 3]));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_show_returns_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Alice']);

        $response = $this->actingAs($admin)->getJson(route('api.users.show', $user));

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Alice']);
    }

    public function test_store_creates_user_with_defaults(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('api.users.store'), [
            'name' => 'Novo',
            'email' => 'novo@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertCreated();
        $response->assertJsonFragment(['role' => 'user']);
        $this->assertDatabaseHas('users', ['email' => 'novo@example.com', 'role' => 'user']);
    }

    public function test_store_creates_user_with_explicit_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->postJson(route('api.users.store'), [
            'name' => 'Admin2',
            'email' => 'admin2@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'admin2@example.com', 'role' => 'admin']);
    }

    public function test_store_fails_on_duplicate_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->actingAs($admin)->postJson(route('api.users.store'), [
            'name' => 'Dup',
            'email' => 'dup@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_store_fails_without_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('api.users.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_update_changes_user_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->putJson(route('api.users.update', $user), [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'New Name']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    /** @dataProvider emptyPasswordProvider */
    public function test_update_does_not_change_password_when_empty(mixed $password): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $oldHash = $user->password;

        $this->actingAs($admin)->putJson(route('api.users.update', $user), [
            'name' => $user->name,
            'password' => $password,
        ]);

        $user->refresh();
        $this->assertEquals($oldHash, $user->password);
    }

    public static function emptyPasswordProvider(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
        ];
    }

    public function test_destroy_deletes_user_and_returns_204(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson(route('api.users.destroy', $target));

        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}
