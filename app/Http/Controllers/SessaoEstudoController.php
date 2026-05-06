<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessaoEstudoRequest;
use App\Http\Requests\UpdateSessaoEstudoRequest;
use App\Models\Assunto;
use App\Models\SessaoEstudo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD de sessões de estudo.
 *
 * Regras importantes:
 * - Sempre escopa por usuário autenticado via `assunto -> materia`.
 * - Para criar, o `assunto_id` deve pertencer ao usuário.
 * - Retorna JSON para consumo pelo front.
 */
class SessaoEstudoController extends Controller
{
    /**
     * Lista sessões de estudo do usuário autenticado com paginação.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $sessoes = SessaoEstudo::query()
            ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->select(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at'])
            ->orderBy('data', 'desc')
            ->paginate($perPage);

        return response()->json($sessoes);
    }

    /**
     * Exibe uma sessão de estudo do usuário autenticado.
     */
    public function show(Request $request, SessaoEstudo $sessaoEstudo): JsonResponse
    {
        $this->ensureOwnership($request, $sessaoEstudo);

        return response()->json(
            $sessaoEstudo->only(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Cria uma sessão de estudo para um assunto do usuário autenticado.
     */
    public function store(StoreSessaoEstudoRequest $request): JsonResponse
    {
        $data = $request->validated();

        $assunto = Assunto::query()
            ->where('id', $data['assunto_id'])
            ->whereHas('materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->first();

        abort_unless($assunto !== null, 404);

        $sessao = SessaoEstudo::create([
            'data' => $data['data'],
            'tipo' => $data['tipo'],
            'horas' => $data['horas'],
            'finalizado' => $data['finalizado'] ?? false,
            'assunto_id' => $assunto->id,
        ]);

        return response()->json(
            $sessao->only(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Atualiza uma sessão de estudo do usuário autenticado.
     */
    public function update(UpdateSessaoEstudoRequest $request, SessaoEstudo $sessaoEstudo): JsonResponse
    {
        $this->ensureOwnership($request, $sessaoEstudo);

        $sessaoEstudo->fill($request->validated());
        $sessaoEstudo->save();

        return response()->json(
            $sessaoEstudo->only(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Remove uma sessão de estudo do usuário autenticado.
     */
    public function destroy(Request $request, SessaoEstudo $sessaoEstudo): JsonResponse
    {
        $this->ensureOwnership($request, $sessaoEstudo);

        $sessaoEstudo->delete();

        return response()->json(null, 204);
    }

    /**
     * Garante que o registro pertence ao usuário autenticado via `assunto -> materia`.
     */
    private function ensureOwnership(Request $request, SessaoEstudo $sessaoEstudo): void
    {
        $sessaoEstudo->loadMissing(['assunto.materia:id,user_id']);

        abort_unless($sessaoEstudo->assunto?->materia?->user_id === $request->user()->id, 404);
    }
}
