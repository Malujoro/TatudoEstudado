<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssuntoRequest;
use App\Http\Requests\UpdateAssuntoRequest;
use App\Models\Assunto;
use App\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD controller for Assuntos, scoped to the authenticated user.
 */
class AssuntoController extends Controller
{
    /**
     * List the authenticated user's assuntos (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $assuntos = Assunto::query()
            ->whereHas('materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->select(['id', 'nome', 'teoria_finalizada', 'materia_id', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($assuntos);
    }

    /**
     * Show a single assunto (must belong to the authenticated user).
     */
    public function show(Request $request, Assunto $assunto): JsonResponse
    {
        $this->ensureOwnership($request, $assunto);

        return response()->json(
            $assunto->only(['id', 'nome', 'teoria_finalizada', 'materia_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Create a new assunto for one of the authenticated user's materias.
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
            'teoria_finalizada' => $data['teoria_finalizada'] ?? false,
        ]);

        return response()->json(
            $assunto->only(['id', 'nome', 'teoria_finalizada', 'materia_id', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Update an assunto (must belong to the authenticated user).
     */
    public function update(UpdateAssuntoRequest $request, Assunto $assunto): JsonResponse
    {
        $this->ensureOwnership($request, $assunto);

        $assunto->fill($request->validated());
        $assunto->save();

        return response()->json(
            $assunto->only(['id', 'nome', 'teoria_finalizada', 'materia_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Delete an assunto (must belong to the authenticated user).
     */
    public function destroy(Request $request, Assunto $assunto): JsonResponse
    {
        $this->ensureOwnership($request, $assunto);

        $assunto->delete();

        return response()->json(null, 204);
    }

    /**
     * Ensure the record belongs to the authenticated user via `materia`.
     */
    private function ensureOwnership(Request $request, Assunto $assunto): void
    {
        $assunto->loadMissing(['materia:id,user_id']);

        abort_unless($assunto->materia?->user_id === $request->user()->id, 404);
    }
}
