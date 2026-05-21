<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * Handles authentication views and actions (login/register/logout, password reset).
 */
class AuthController extends Controller
{
    /**
     * Show the login page.
     *
     * @return View
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the registration page.
     *
     * @return View
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Attempt to authenticate the user.
     *
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['email' => 'Credenciais inválidas.'])
            ->onlyInput('email');
    }

    /**
     * Register a new user and log them in.
     *
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'horario_semanal' => [
                'domingo' => 0,
                'segunda' => 0,
                'terca' => 0,
                'quarta' => 0,
                'quinta' => 0,
                'sexta' => 0,
                'sabado' => 0,
            ],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }

    /**
     * Log out the current user.
     *
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Send a password reset link to the given email.
     *
     * @return RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link enviado!')
            : back()->withErrors(['email' => 'Erro ao enviar link']);
    }

    /**
     * Reset the user's password using the provided token.
     *
     * @return RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // dd($status);

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('status', 'Senha redefinida!')
            : back()->withErrors(['email' => 'Token inválido']);
    }
}
