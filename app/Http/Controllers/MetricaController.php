<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMetricaRequest;
use App\Http\Requests\UpdateMetricaRequest;
use App\Models\Assunto;
use App\Models\Metrica;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD de métricas.
 *
 * Regras importantes:
 * - 1 métrica por assunto (assunto_id é UNIQUE).
 * - Sempre escopa por usuário autenticado via `assunto -> materia`.
 * - Para criar, o `assunto_id` deve pertencer ao usuário.
 * - Retorna JSON para consumo pelo front.
 */
class MetricaController extends Controller
{
    /**
     * Lista métricas do usuário autenticado com paginação.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        $metricas = Metrica::query()
            ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->select(['id', 'acertos', 'erros', 'assunto_id', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($metricas);
    }

    /**
     * Exibe uma métrica do usuário autenticado.
     */
    public function show(Request $request, Metrica $metrica): JsonResponse
    {
        $this->ensureOwnership($request, $metrica);

        return response()->json(
            $metrica->only(['id', 'acertos', 'erros', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Cria uma métrica para um assunto do usuário autenticado.
     */
    public function store(StoreMetricaRequest $request): JsonResponse
    {
        $data = $request->validated();

        $assunto = Assunto::query()
            ->where('id', $data['assunto_id'])
            ->whereHas('materia', fn ($q) => $q->where('user_id', $request->user()->id))
            ->first();

        abort_unless($assunto !== null, 404);

        $metrica = Metrica::create([
            'acertos' => $data['acertos'] ?? 0,
            'erros' => $data['erros'] ?? 0,
            'assunto_id' => $assunto->id,
        ]);

        return response()->json(
            $metrica->only(['id', 'acertos', 'erros', 'assunto_id', 'created_at', 'updated_at']),
            201
        );
    }

    /**
     * Atualiza uma métrica do usuário autenticado.
     */
    public function update(UpdateMetricaRequest $request, Metrica $metrica): JsonResponse
    {
        $this->ensureOwnership($request, $metrica);

        $metrica->fill($request->validated());
        $metrica->save();

        return response()->json(
            $metrica->only(['id', 'acertos', 'erros', 'assunto_id', 'created_at', 'updated_at'])
        );
    }

    /**
     * Remove uma métrica do usuário autenticado.
     */
    public function destroy(Request $request, Metrica $metrica): JsonResponse
    {
        $this->ensureOwnership($request, $metrica);

        $metrica->delete();

        return response()->json(null, 204);
    }

    /**
     * Garante que o registro pertence ao usuário autenticado via `assunto -> materia`.
     */
    private function ensureOwnership(Request $request, Metrica $metrica): void
    {
        $metrica->loadMissing(['assunto.materia:id,user_id']);

        abort_unless($metrica->assunto?->materia?->user_id === $request->user()->id, 404);
    }
}
