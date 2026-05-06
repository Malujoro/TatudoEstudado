<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMateriaRequest;
use App\Http\Requests\UpdateMateriaRequest;
use App\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD de matérias.
 *
 * Regras importantes:
 * - Sempre escopa por `user_id` (usuário autenticado).
 * - `user_id` não é aceito do cliente; é definido pelo backend.
 * - Retorna JSON para consumo pelo front.
 */
class MateriaController extends Controller
{
    /**
     * Lista matérias do usuário autenticado com paginação.
     *
     * Query params suportados:
     * - `per_page` (int): quantidade por página (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $materias = Materia::query()
            ->where('user_id', $request->user()->id)
            ->select(['id', 'nome', 'user_id', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($materias);
    }

    /**
     * Exibe uma matéria do usuário autenticado.
     */
    public function show(Request $request, Materia $materia): JsonResponse
    {
        $this->ensureOwnership($request, $materia);

        return response()->json(
            $materia->only(['id', 'nome', 'user_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Cria uma matéria para o usuário autenticado.
     */
    public function store(StoreMateriaRequest $request): JsonResponse
    {
        $materia = Materia::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json(
            $materia->only(['id', 'nome', 'user_id', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Atualiza uma matéria do usuário autenticado.
     */
    public function update(UpdateMateriaRequest $request, Materia $materia): JsonResponse
    {
        $this->ensureOwnership($request, $materia);

        $materia->fill($request->validated());
        $materia->save();

        return response()->json(
            $materia->only(['id', 'nome', 'user_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Remove uma matéria do usuário autenticado.
     */
    public function destroy(Request $request, Materia $materia): JsonResponse
    {
        $this->ensureOwnership($request, $materia);

        $materia->delete();

        return response()->json(null, 204);
    }

    /**
     * Garante que o registro pertence ao usuário autenticado.
     *
     * Retorna 404 para evitar enumeração de IDs por terceiros.
     */
    private function ensureOwnership(Request $request, Materia $materia): void
    {
        abort_unless($materia->user_id === $request->user()->id, 404);
    }
}
