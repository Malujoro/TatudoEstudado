<?php

use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CadernoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MetricaController;
use App\Http\Controllers\SessaoEstudoController;
use App\Http\Controllers\UserController;
use App\Models\Assunto;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return view('home');
    }

    return redirect()->route('login');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store')
    ->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register')
    ->middleware('guest');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store')
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Recuperação de senha:
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

Route::view('/reset-password', 'auth.reset-password')->name('password.reset');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::post('/reset-password', [AuthController::class, 'resetPassword']);
// ###

Route::middleware('auth')->group(function () {
    Route::get('/materias', function (Request $request) {
        // Carrega todas as matérias do usuário
        $materias = Materia::where('user_id', $request->user()->id)->withCount('assuntos')->orderBy('nome')->get();

        return view('materias.index', compact('materias'));
    })->name('materias.index');

    Route::get('/assuntos', function (Request $request) {
        // Carrega as matérias e seus respectivos assuntos
        $materias = Materia::where('user_id', $request->user()->id)->orderBy('nome')->get();
        $assuntos = Assunto::whereIn('materia_id', $materias->pluck('id'))->with('materia')->orderBy('nome')->get();

        return view('assuntos.index', compact('materias', 'assuntos'));
    })->name('assuntos.index');

    Route::get('/perfil', function (Request $request) {
        $user = $request->user();
        // Carrega as matérias com a contagem de assuntos
        $materias = Materia::where('user_id', $user->id)->withCount('assuntos')->orderBy('nome')->get();

        return view('profile', compact('user', 'materias'));
    })->name('profile');

    Route::post('/perfil', function (Request $request) {
        $data = $request->validate([
            'horas_por_dia' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Disponibilidade atualizada!');
    })->name('profile.update');
});
/*
|--------------------------------------------------------------------------
| API (JSON) - Exemplo de CRUD
|--------------------------------------------------------------------------
|
| Estas rotas retornam JSON e servem como base para os demais CRUDs.
| Estão protegidas por `auth` (sessão). Para uso via front, basta consumir
| os endpoints e enviar o cookie de sessão (ou adaptar para token depois).
|
*/

Route::middleware('auth')
    ->prefix('api')
    ->name('api.')
    ->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('materias', MateriaController::class);
        Route::apiResource('assuntos', AssuntoController::class);
        Route::apiResource('cadernos', CadernoController::class);
        Route::apiResource('metricas', MetricaController::class);
        Route::apiResource('sessoes-estudo', SessaoEstudoController::class)
            ->parameters(['sessoes-estudo' => 'sessaoEstudo']);
    });
