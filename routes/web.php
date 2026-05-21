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
use App\Models\SessaoEstudo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * Application Web and API Routes.
 *
 * This file defines all the HTTP routes for the application, including
 * dashboard views, authentication, and the JSON API endpoints.
 */

/**
 * Home Dashboard.
 *
 * Displays the study calendar for a specific week.
 *
 * @param  Request  $request  Includes 'week' (int) as a query parameter.
 * @return View|RedirectResponse
 */
Route::get('/', function (Request $request) {
    if (auth()->check()) {
        $user = $request->user();
        $weekOffset = (int) $request->query('week', 0);
        $inicioSemana = Carbon::today()->startOfWeek(Carbon::SUNDAY)->addWeeks($weekOffset);
        $fimSemana = $inicioSemana->copy()->addDays(6);

        $sessoesSemana = SessaoEstudo::query()
            ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $user->id))
            ->whereBetween('data', [$inicioSemana->toDateString(), $fimSemana->toDateString()])
            ->with([
                'assunto:id,nome,materia_id',
                'assunto.materia:id,nome',
                'assunto.metrica:id,assunto_id,acertos,erros',
                'assunto.caderno:id,assunto_id,conteudo',
            ])
            ->orderBy('data')
            ->orderBy('created_at')
            ->get();

        $sessoesPorDia = $sessoesSemana->groupBy(fn ($sessao) => $sessao->data->toDateString());

        $diaLabels = [
            0 => 'Domingo',
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            6 => 'Sábado',
        ];

        $dias = collect(range(0, 6))->map(function ($offset) use ($inicioSemana, $sessoesPorDia, $diaLabels) {
            $data = $inicioSemana->copy()->addDays($offset);
            $key = $data->toDateString();

            return [
                'data' => $key,
                'label' => $diaLabels[$data->dayOfWeek] ?? $data->isoFormat('dddd'),
                'sessoes' => $sessoesPorDia->get($key, collect()),
            ];
        });

        return view('home', [
            'dias' => $dias,
            'weekOffset' => $weekOffset,
            'inicioSemana' => $inicioSemana,
            'fimSemana' => $fimSemana,
        ]);
    }

    return redirect()->route('login');
})->name('home');

/**
 * Daily Goals.
 *
 * Lists all unfinished study sessions due by or before today.
 *
 * @param  Request  $request
 * @return View|RedirectResponse
 */
Route::get('/metas-diarias', function (Request $request) {
    if (auth()->check()) {
        $user = $request->user();

        $pendentes = SessaoEstudo::query()
            ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $user->id))
            ->whereDate('data', '<=', Carbon::today()->toDateString())
            ->where('finalizado', false)
            ->with([
                'assunto:id,nome,materia_id',
                'assunto.materia:id,nome',
                'assunto.metrica:id,assunto_id,acertos,erros',
                'assunto.caderno:id,assunto_id,conteudo',
            ])
            ->orderBy('data')
            ->orderBy('created_at')
            ->get();

        $diaLabels = [
            0 => 'Domingo',
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            6 => 'Sábado',
        ];

        $pendentesPorDia = $pendentes->groupBy(fn ($sessao) => $sessao->data->toDateString());

        $diasPendentes = $pendentesPorDia->map(function ($sessoes, $data) use ($diaLabels) {
            $carbon = Carbon::parse($data);

            return [
                'data' => $data,
                'label' => $diaLabels[$carbon->dayOfWeek] ?? $carbon->isoFormat('dddd'),
                'atrasada' => $carbon->isBefore(Carbon::today()),
                'sessoes' => $sessoes,
            ];
        })->values();

        return view('metas-diarias', [
            'diasPendentes' => $diasPendentes,
        ]);
    }

    return redirect()->route('login');
})->name('metas.diarias');

/**
 * Authentication Routes (Login, Register, Logout).
 */
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

// Password Recovery Routes
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::view('/reset-password', 'auth.reset-password')->name('password.reset');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/**
 * Protected Web Interface Routes.
 *
 * Requires user authentication.
 */
Route::middleware('auth')->group(function () {
    /**
     * List all Subjects (Matérias).
     *
     * @return View
     */
    Route::get('/materias', function (Request $request) {
        $materias = Materia::where('user_id', $request->user()->id)->withCount('assuntos')->orderBy('nome')->get();

        return view('materias.index', compact('materias'));
    })->name('materias.index');

    /**
     * List all Topics (Assuntos).
     *
     * @return View
     */
    Route::get('/assuntos', function (Request $request) {
        $materias = Materia::where('user_id', $request->user()->id)->orderBy('nome')->get();
        $assuntos = Assunto::whereIn('materia_id', $materias->pluck('id'))->with('materia')->orderBy('nome')->get();

        return view('assuntos.index', compact('materias', 'assuntos'));
    })->name('assuntos.index');

    /**
     * User Profile and Availability View.
     *
     * @return View
     */
    Route::get('/perfil', function (Request $request) {
        $user = $request->user();
        $materias = Materia::where('user_id', $user->id)->withCount('assuntos')->orderBy('nome')->get();

        return view('profile', compact('user', 'materias'));
    })->name('profile');

    /**
     * Update User Weekly Availability.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    Route::post('/perfil', function (Request $request) {
        $data = $request->validate([
            'horario_semanal' => ['required', 'array'],
            'horario_semanal.domingo' => ['required', 'numeric', 'min:0', 'max:24'],
            'horario_semanal.segunda' => ['required', 'numeric', 'min:0', 'max:24'],
            'horario_semanal.terca' => ['required', 'numeric', 'min:0', 'max:24'],
            'horario_semanal.quarta' => ['required', 'numeric', 'min:0', 'max:24'],
            'horario_semanal.quinta' => ['required', 'numeric', 'min:0', 'max:24'],
            'horario_semanal.sexta' => ['required', 'numeric', 'min:0', 'max:24'],
            'horario_semanal.sabado' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Disponibilidade atualizada!');
    })->name('profile.update');
});

/**
 * JSON API Routes.
 *
 * Provides RESTful endpoints for CRUD operations and schedule generation.
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
        Route::post('cronograma/gerar', [SessaoEstudoController::class, 'gerarCronograma'])
            ->name('cronograma.gerar');
        Route::post('sessoes-estudo/{sessaoEstudo}/finalizar', [SessaoEstudoController::class, 'finalizar'])
            ->name('sessoes-estudo.finalizar');
        Route::apiResource('sessoes-estudo', SessaoEstudoController::class)
            ->parameters(['sessoes-estudo' => 'sessaoEstudo']);
    });
