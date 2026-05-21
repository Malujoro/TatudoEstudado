<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMateriaRequest;
use App\Http\Requests\UpdateMateriaRequest;
use App\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD controller for Materias, scoped to the authenticated user.
 */
class MateriaController extends Controller
{
    /**
     * List the authenticated user's materias (paginated).
     *
     * @param Request $request
     * @return JsonResponse
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
        * Show a single materia (must belong to the authenticated user).
        *
        * @param Request $request
        * @param Materia $materia
        * @return JsonResponse
     */
    public function show(Request $request, Materia $materia): JsonResponse
    {
        $this->ensureOwnership($request, $materia);

        return response()->json(
            $materia->only(['id', 'nome', 'user_id', 'created_at', 'updated_at'])
        );
    }

    /**
        * Create a new materia for the authenticated user.
        *
        * @param StoreMateriaRequest $request
        * @return JsonResponse
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
        * Update an existing materia (must belong to the authenticated user).
        *
        * @param UpdateMateriaRequest $request
        * @param Materia $materia
        * @return JsonResponse
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
        * Delete a materia (must belong to the authenticated user).
        *
        * @param Request $request
        * @param Materia $materia
        * @return JsonResponse
     */
    public function destroy(Request $request, Materia $materia): JsonResponse
    {
        $this->ensureOwnership($request, $materia);

        $materia->delete();

        return response()->json(null, 204);
    }

    /**
     * Ensure the record belongs to the authenticated user.
     *
     * @param Request $request
     * @param Materia $materia
     * @return void
     */
    private function ensureOwnership(Request $request, Materia $materia): void
    {
        abort_unless($materia->user_id === $request->user()->id, 404);
    }
}
