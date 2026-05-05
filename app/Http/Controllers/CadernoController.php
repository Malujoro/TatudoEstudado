<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCadernoRequest;
use App\Http\Requests\UpdateCadernoRequest;
use App\Models\Assunto;
use App\Models\Caderno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD de cadernos.
 *
 * Regras importantes:
 * - 1 caderno por assunto (assunto_id é UNIQUE).
 * - Sempre escopa por usuário autenticado via `assunto -> materia`.
 * - Para criar, o `assunto_id` deve pertencer ao usuário.
 * - Retorna JSON para consumo pelo front.
 */
class CadernoController extends Controller
{
    /**
     * Lista cadernos do usuário autenticado com paginação.
     *
     * Query params suportados:
     * - `per_page` (int): quantidade por página (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $cadernos = Caderno::query()
            ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->select(['id', 'conteudo', 'assunto_id', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($cadernos);
    }

    /**
     * Exibe um caderno do usuário autenticado.
     */
    public function show(Request $request, Caderno $caderno): JsonResponse
    {
        $this->ensureOwnership($request, $caderno);

        return response()->json(
            $caderno->only(['id', 'conteudo', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Cria um caderno para um assunto do usuário autenticado.
     */
    public function store(StoreCadernoRequest $request): JsonResponse
    {
        $data = $request->validated();

        $assunto = Assunto::query()
            ->where('id', $data['assunto_id'])
            ->whereHas('materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->first();

        abort_unless($assunto !== null, 404);

        $caderno = Caderno::create([
            'conteudo' => $data['conteudo'],
            'assunto_id' => $assunto->id,
        ]);

        return response()->json(
            $caderno->only(['id', 'conteudo', 'assunto_id', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Atualiza um caderno do usuário autenticado.
     */
    public function update(UpdateCadernoRequest $request, Caderno $caderno): JsonResponse
    {
        $this->ensureOwnership($request, $caderno);

        $caderno->fill($request->validated());
        $caderno->save();

        return response()->json(
            $caderno->only(['id', 'conteudo', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Remove um caderno do usuário autenticado.
     */
    public function destroy(Request $request, Caderno $caderno): JsonResponse
    {
        $this->ensureOwnership($request, $caderno);

        $caderno->delete();

        return response()->json(null, 204);
    }

    /**
     * Garante que o registro pertence ao usuário autenticado via `assunto -> materia`.
     *
     * Retorna 404 para evitar enumeração de IDs por terceiros.
     */
    private function ensureOwnership(Request $request, Caderno $caderno): void
    {
        $caderno->loadMissing(['assunto.materia:id,user_id']);

        abort_unless($caderno->assunto?->materia?->user_id === $request->user()->id, 404);
    }
}
