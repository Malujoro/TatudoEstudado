<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Testes para AuthController
 *
 * Cenários cobertos:
 * - showLogin / showRegister: retornam as views corretas
 * - login: sucesso (redireciona), credenciais inválidas (retorna com erro)
 * - login: validação de campos obrigatórios (email e senha ausentes)
 * - register: sucesso (cria usuário, faz login, redireciona)
 * - register: email duplicado retorna erro de validação
 * - register: senha sem confirmação retorna erro
 * - register: senha curta (< 6 chars) retorna erro
 * - logout: invalida sessão e redireciona para login
 * - forgotPassword: email válido envia link e redireciona de volta
 * - forgotPassword: falha no envio retorna erro
 * - forgotPassword: email inválido falha validação
 * - resetPassword: token válido redefine senha e redireciona para /login
 * - resetPassword: token inválido retorna erro
 * - resetPassword: senha muito curta falha validação
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // showLogin / showRegister
    // -----------------------------------------------------------------------

    public function test_show_login_returns_view(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_show_register_returns_view(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    // -----------------------------------------------------------------------
    // login
    // -----------------------------------------------------------------------

    public function test_login_with_valid_credentials_redirects(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_password_returns_error(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'wrong',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_with_nonexistent_email_returns_error(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'any',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_requires_email(): void
    {
        $response = $this->post(route('login'), ['password' => 'secret123']);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'not-an-email',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post(route('login'), ['email' => 'user@example.com']);

        $response->assertSessionHasErrors(['password']);
    }

    // -----------------------------------------------------------------------
    // register
    // -----------------------------------------------------------------------

    public function test_register_creates_user_and_redirects(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'name' => 'João Silva',
            'role' => 'user',
        ]);
    }

    public function test_register_stores_default_horario_semanal(): void
    {
        $this->post(route('register'), [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $user = User::where('email', 'ana@example.com')->first();
        $horario = $user->horario_semanal;

        foreach (['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'] as $dia) {
            $this->assertEquals(0, $horario[$dia]);
        }
    }

    public function test_register_with_duplicate_email_fails(): void
    {
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Outro',
            'email' => 'dup@example.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'password' => 'senha123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'teste@example.com']);
    }

    public function test_register_requires_password_min_6_chars(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_register_requires_name(): void
    {
        $response = $this->post(route('register'), [
            'email' => 'teste@example.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_register_requires_valid_email(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Teste',
            'email' => 'invalido',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    // -----------------------------------------------------------------------
    // logout
    // -----------------------------------------------------------------------

    public function test_logout_invalidates_session_and_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // forgotPassword
    // -----------------------------------------------------------------------

    public function test_forgot_password_sends_link_for_existing_email(): void
    {
        User::factory()->create(['email' => 'reset@example.com']);

        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $response = $this->post('/forgot-password', [
            'email' => 'reset@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Link enviado!');
    }

    public function test_forgot_password_returns_error_on_failure(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::INVALID_USER);

        $response = $this->post('/forgot-password', [
            'email' => 'noone@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'not-valid',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    // -----------------------------------------------------------------------
    // resetPassword
    // -----------------------------------------------------------------------

    public function test_reset_password_success_redirects_to_login(): void
    {
        Password::shouldReceive('reset')
            ->once()
            ->andReturnUsing(function ($creds, $callback) {
                $user = User::factory()->make(); // instância sem persistir
                $callback($user, 'newpassword');

                return 'passwords.reset';
            });

        $response = $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => 'user@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Senha redefinida!');
    }

    public function test_reset_password_with_invalid_token_returns_error(): void
    {
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        $response = $this->post('/reset-password', [
            'token' => 'bad-token',
            'email' => 'user@example.com',
            'password' => 'newpassword12',
            'password_confirmation' => 'newpassword12',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_requires_min_8_chars(): void
    {
        $response = $this->post('/reset-password', [
            'token' => 'tok',
            'email' => 'user@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_reset_password_requires_token(): void
    {
        $response = $this->post('/reset-password', [
            'email' => 'user@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertSessionHasErrors(['token']);
    }
}
