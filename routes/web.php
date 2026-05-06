<?php

use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CadernoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MetricaController;
use App\Http\Controllers\SessaoEstudoController;
use App\Http\Controllers\UserController;
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

Route::get('/materias', function () {
    return view('materias.index');
})->name('materias.index')->middleware('auth');

Route::get('/assuntos', function () {
    return view('assuntos.index');
})->name('assuntos.index')->middleware('auth');
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
