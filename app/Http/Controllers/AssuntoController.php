<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssuntoRequest;
use App\Http\Requests\UpdateAssuntoRequest;
use App\Models\Assunto;
use App\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD de assuntos.
 *
 * Regras importantes:
 * - Sempre escopa por usuário autenticado via relação com `materias`.
 * - Para criar assunto, a `materia_id` deve pertencer ao usuário.
 * - Retorna JSON para consumo pelo front.
 */
class AssuntoController extends Controller
{
    /**
     * Lista assuntos do usuário autenticado com paginação.
     *
     * Query params suportados:
     * - `per_page` (int): quantidade por página (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $assuntos = Assunto::query()
            ->whereHas('materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->select(['id', 'nome', 'materia_id', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($assuntos);
    }

    /**
     * Exibe um assunto do usuário autenticado.
     */
    public function show(Request $request, Assunto $assunto): JsonResponse
    {
        $this->ensureOwnership($request, $assunto);

        return response()->json(
            $assunto->only(['id', 'nome', 'materia_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Cria um assunto para uma matéria do usuário autenticado.
     */
    public function store(StoreAssuntoRequest $request): JsonResponse
    {
        $data = $request->validated();

        $materia = Materia::query()
            ->where('id', $data['materia_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        abort_unless($materia !== null, 404);

        $assunto = Assunto::create([
            'nome' => $data['nome'],
            'materia_id' => $materia->id,
        ]);

        return response()->json(
            $assunto->only(['id', 'nome', 'materia_id', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Atualiza um assunto do usuário autenticado.
     */
    public function update(UpdateAssuntoRequest $request, Assunto $assunto): JsonResponse
    {
        $this->ensureOwnership($request, $assunto);

        $assunto->fill($request->validated());
        $assunto->save();

        return response()->json(
            $assunto->only(['id', 'nome', 'materia_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Remove um assunto do usuário autenticado.
     */
    public function destroy(Request $request, Assunto $assunto): JsonResponse
    {
        $this->ensureOwnership($request, $assunto);

        $assunto->delete();

        return response()->json(null, 204);
    }

    /**
     * Garante que o registro pertence ao usuário autenticado via `materia`.
     *
     * Retorna 404 para evitar enumeração de IDs por terceiros.
     */
    private function ensureOwnership(Request $request, Assunto $assunto): void
    {
        $assunto->loadMissing(['materia:id,user_id']);

        abort_unless($assunto->materia?->user_id === $request->user()->id, 404);
    }
}
