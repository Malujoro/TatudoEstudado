<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessaoEstudoRequest;
use App\Http\Requests\UpdateSessaoEstudoRequest;
use App\Models\Assunto;
use App\Models\Metrica;
use App\Models\SessaoEstudo;
use App\Services\CronogramaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
     * Gera o cronograma de estudo para os próximos 15 dias.
     */
    public function gerarCronograma(Request $request, CronogramaService $cronogramaService): JsonResponse
    {
        $inicio = $request->query('inicio');
        $limpar = $request->boolean('limpar', true);

        $resultado = $cronogramaService->gerar(
            $request->user(),
            $inicio ? Carbon::parse($inicio) : null,
            15,
            $limpar
        );

        return response()->json($resultado, 201);
    }

    /**
     * Finaliza uma sessão de estudo e atualiza métricas (quando aplicável).
     */
    public function finalizar(Request $request, SessaoEstudo $sessaoEstudo): JsonResponse
    {
        $this->ensureOwnership($request, $sessaoEstudo);

        $data = $request->validate([
            'questoes' => ['sometimes', 'integer', 'min:0'],
            'acertos' => ['sometimes', 'integer', 'min:0'],
        ]);

        if ($sessaoEstudo->finalizado) {
            return response()->json(
                $sessaoEstudo->only(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at'])
            );
        }

        if ($sessaoEstudo->tipo === 'exercicio') {
            $questoes = $data['questoes'] ?? null;
            $acertos = $data['acertos'] ?? null;

            if ($questoes === null || $acertos === null) {
                return response()->json([
                    'message' => 'Informe a quantidade de questões e acertos.',
                ], 422);
            }

            if ($acertos > $questoes) {
                return response()->json([
                    'message' => 'Acertos não podem ser maiores que questões.',
                ], 422);
            }

            $erros = $questoes - $acertos;

            $metrica = Metrica::firstOrCreate(
                ['assunto_id' => $sessaoEstudo->assunto_id],
                ['acertos' => 0, 'erros' => 0]
            );

            $metrica->acertos += $acertos;
            $metrica->erros += $erros;
            $metrica->save();
        }

        $sessaoEstudo->finalizado = true;
        $sessaoEstudo->save();

        return response()->json(
            $sessaoEstudo->only(['id', 'data', 'tipo', 'horas', 'finalizado', 'assunto_id', 'created_at', 'updated_at'])
        );
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
