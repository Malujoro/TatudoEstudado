<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCadernoRequest;
use App\Http\Requests\UpdateCadernoRequest;
use App\Models\Assunto;
use App\Models\Caderno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD controller for Cadernos, scoped to the authenticated user.
 */
class CadernoController extends Controller
{
    /**
     * List the authenticated user's cadernos (paginated).
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
     * Show a single caderno (must belong to the authenticated user).
     */
    public function show(Request $request, Caderno $caderno): JsonResponse
    {
        $this->ensureOwnership($request, $caderno);

        return response()->json(
            $caderno->only(['id', 'conteudo', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Create a new caderno for one of the authenticated user's assuntos.
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
     * Update a caderno (must belong to the authenticated user).
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
     * Delete a caderno (must belong to the authenticated user).
     */
    public function destroy(Request $request, Caderno $caderno): JsonResponse
    {
        $this->ensureOwnership($request, $caderno);

        $caderno->delete();

        return response()->json(null, 204);
    }

    /**
     * Ensure the record belongs to the authenticated user via `assunto -> materia`.
     */
    private function ensureOwnership(Request $request, Caderno $caderno): void
    {
        $caderno->loadMissing(['assunto.materia:id,user_id']);

        abort_unless($caderno->assunto?->materia?->user_id === $request->user()->id, 404);
    }
}
