<?php

namespace App\Services;

use App\Models\Assunto;
use App\Models\SessaoEstudo;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service responsible for generating study schedules.
 * It calculates and suggests study sessions based on user availability,
 * topic completion, and performance metrics.
 */
class CronogramaService
{
    private const DAY_KEYS = [
        'domingo',
        'segunda',
        'terca',
        'quarta',
        'quinta',
        'sexta',
        'sabado',
    ];

    private const SESSION_MINUTES = [
        'teoria' => 30,
        'exercicio' => 30,
        'revisao' => 15,
    ];

    /**
     * Generates a study schedule for the upcoming days.
     *
     * @param  User  $user  Target user (availability is read from horario_semanal).
     * @param  Carbon|null  $inicio  Start date (defaults to today).
     * @param  int  $dias  Number of days to generate (defaults to 15).
     * @param  bool  $limpar  When true, removes unfinished sessions in the generated range.
     * @return array<string, mixed> Schedule payload: inicio, fim, total, sessoes.
     */
    public function gerar(User $user, ?Carbon $inicio = null, int $dias = 15, bool $limpar = true): array
    {
        $inicio = $inicio?->copy()->startOfDay() ?? Carbon::today();
        $dias = $dias > 0 ? $dias : 15;
        $fim = $inicio->copy()->addDays($dias - 1);

        $assuntos = Assunto::query()
            ->whereHas('materia', fn ($q) => $q->where('user_id', $user->id))
            ->with('metrica:id,assunto_id,acertos,erros')
            ->get(['id', 'nome', 'materia_id', 'teoria_finalizada', 'tipo']);

        if ($assuntos->isEmpty()) {
            return [
                'inicio' => $inicio->toDateString(),
                'fim' => $fim->toDateString(),
                'sessoes' => [],
                'total' => 0,
            ];
        }

        $lastSessions = SessaoEstudo::query()
            ->select('assunto_id', DB::raw('MAX(data) as last_date'))
            ->whereIn('assunto_id', $assuntos->pluck('id'))
            ->groupBy('assunto_id')
            ->get()
            ->keyBy('assunto_id');

        $states = [];
        foreach ($assuntos as $assunto) {
            $acertos = (int) ($assunto->metrica?->acertos ?? 0);
            $erros = (int) ($assunto->metrica?->erros ?? 0);
            $total = $acertos + $erros;
            $errorRate = $total > 0 ? $erros / $total : 0.0;

            $states[$assunto->id] = [
                'assunto' => $assunto,
                'materia_id' => $assunto->materia_id,
                'error_rate' => $errorRate,
                'last_date' => $lastSessions[$assunto->id]->last_date ?? null,
                'scheduled_count' => 0,
                'teoria_finalizada' => (bool) $assunto->teoria_finalizada,
                'type_counts' => [
                    'teoria' => 0,
                    'exercicio' => 0,
                    'revisao' => 0,
                ],
            ];
        }

        if ($limpar) {
            SessaoEstudo::query()
                ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
                ->where('finalizado', false)
                ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $user->id))
                ->delete();
        }

        $sessoesExistentes = SessaoEstudo::query()
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->whereIn('assunto_id', $assuntos->pluck('id'))
            ->get();

        $minutosExistentesPorDia = [];
        $ultimosTiposPorDia = [];
        $ultimasMateriasPorDia = [];

        foreach ($sessoesExistentes as $sessao) {
            $dataStr = Carbon::parse($sessao->data)->toDateString();
            $minutos = (int) round((float) $sessao->horas * 60);

            $minutosExistentesPorDia[$dataStr] = ($minutosExistentesPorDia[$dataStr] ?? 0) + $minutos;

            if (isset($states[$sessao->assunto_id])) {
                $states[$sessao->assunto_id]['scheduled_count']++;
                $states[$sessao->assunto_id]['type_counts'][$sessao->tipo] =
                    ($states[$sessao->assunto_id]['type_counts'][$sessao->tipo] ?? 0) + 1;

                $ultimosTiposPorDia[$dataStr][] = $sessao->tipo;
                $ultimasMateriasPorDia[$dataStr][] = $states[$sessao->assunto_id]['materia_id'];
            }
        }

        $remainingAssuntos = array_keys($states);
        $sessoesParaCriar = [];

        for ($offset = 0; $offset < $dias; $offset++) {
            $dia = $inicio->copy()->addDays($offset);
            $diaStr = $dia->toDateString();
            $diaKey = self::DAY_KEYS[$dia->dayOfWeek] ?? 'domingo';
            $horas = (float) ($user->horario_semanal[$diaKey] ?? 0);
            $minutosRestantes = (int) round($horas * 60);

            $minutosRestantes -= $minutosExistentesPorDia[$diaStr] ?? 0;

            $ultimosTipos = $ultimosTiposPorDia[$diaStr] ?? [];
            $ultimasMaterias = $ultimasMateriasPorDia[$diaStr] ?? [];

            $ultimosTipos = array_slice($ultimosTipos, -2);
            $ultimasMaterias = array_slice($ultimasMaterias, -2);

            while ($minutosRestantes >= self::SESSION_MINUTES['revisao']) {
                $candidatos = ! empty($remainingAssuntos) ? $remainingAssuntos : array_keys($states);
                $candidatosTentativa = $candidatos;

                $assuntoId = null;
                $tipo = null;

                while (! empty($candidatosTentativa)) {
                    $assuntoId = $this->escolherAssunto($states, $candidatosTentativa, $dia, $ultimasMaterias);
                    if ($assuntoId === null) {
                        break;
                    }

                    $tipo = $this->escolherTipo($states[$assuntoId], $minutosRestantes, $ultimosTipos);
                    if ($tipo !== null) {
                        break;
                    }

                    // Este assunto não consegue gerar sessão agora. Tenta outro.
                    $candidatosTentativa = array_values(array_diff($candidatosTentativa, [$assuntoId]));
                    $remainingAssuntos = array_values(array_diff($remainingAssuntos, [$assuntoId]));
                    $assuntoId = null;
                }

                if ($assuntoId === null || $tipo === null) {
                    break;
                }

                $minutosSessao = self::SESSION_MINUTES[$tipo];
                if ($minutosSessao > $minutosRestantes) {
                    break;
                }

                $sessoesParaCriar[] = [
                    'data' => $dia->toDateString(),
                    'tipo' => $tipo,
                    'horas' => $minutosSessao / 60,
                    'finalizado' => false,
                    'assunto_id' => $assuntoId,
                ];

                $minutosRestantes -= $minutosSessao;
                $states[$assuntoId]['scheduled_count']++;
                $states[$assuntoId]['type_counts'][$tipo]++;
                $states[$assuntoId]['last_date'] = $dia->toDateString();

                $ultimosTipos[] = $tipo;
                if (count($ultimosTipos) > 2) {
                    array_shift($ultimosTipos);
                }

                $ultimasMaterias[] = $states[$assuntoId]['materia_id'];
                if (count($ultimasMaterias) > 2) {
                    array_shift($ultimasMaterias);
                }

                $remainingAssuntos = array_values(array_diff($remainingAssuntos, [$assuntoId]));
            }
        }

        $criadas = [];
        foreach ($sessoesParaCriar as $sessaoData) {
            $criadas[] = SessaoEstudo::create($sessaoData);
        }

        return [
            'inicio' => $inicio->toDateString(),
            'fim' => $fim->toDateString(),
            'total' => count($criadas),
            'sessoes' => collect($criadas)
                ->map(fn (SessaoEstudo $sessao) => $sessao->only([
                    'id',
                    'data',
                    'tipo',
                    'horas',
                    'finalizado',
                    'assunto_id',
                    'created_at',
                    'updated_at',
                ]))
                ->values(),
        ];
    }

    /**
     * Chooses the next topic to schedule based on error rate, last study date, and recent subjects.
     *
     * @param  array<string, mixed>  $states  In-memory scheduling state indexed by assunto id.
     * @param  array<int, string>  $candidatos  Candidate assunto ids.
     * @param  Carbon  $dia  Day being scheduled.
     * @param  array<int, string>  $ultimasMaterias  Recently scheduled materia ids (used to avoid repetition).
     * @return string|null The chosen assunto id or null.
     */
    private function escolherAssunto(array $states, array $candidatos, Carbon $dia, array $ultimasMaterias): ?string
    {
        $melhorId = null;
        $melhorScore = null;

        $materiaBloqueada = null;
        if (count($ultimasMaterias) >= 2) {
            $ultima = $ultimasMaterias[count($ultimasMaterias) - 1];
            $penultima = $ultimasMaterias[count($ultimasMaterias) - 2];
            if ($ultima === $penultima) {
                $materiaBloqueada = $ultima;
            }
        }

        $filtrados = $materiaBloqueada
            ? array_values(array_filter($candidatos, fn ($id) => $states[$id]['materia_id'] !== $materiaBloqueada))
            : $candidatos;

        $candidatos = ! empty($filtrados) ? $filtrados : $candidatos;

        foreach ($candidatos as $assuntoId) {
            $state = $states[$assuntoId];
            $score = $this->calcularScore($state, $dia);

            if ($melhorScore === null || $score > $melhorScore) {
                $melhorScore = $score;
                $melhorId = $assuntoId;
            }
        }

        return $melhorId;
    }

    /**
     * Calculates a score for a topic to determine its priority in the schedule.
     *
     * @param  array<string, mixed>  $state  Current assunto scheduling state.
     * @param  Carbon  $dia  Day being scheduled.
     * @return float Priority score (higher means more likely to be scheduled).
     *
     * @internal This method is for internal use within the service.
     */
    private function calcularScore(array $state, Carbon $dia): float
    {
        $errorRate = (float) $state['error_rate'];
        $lastDate = $state['last_date'] ? Carbon::parse($state['last_date']) : null;
        $daysSince = $lastDate ? $lastDate->diffInDays($dia) : 14;
        $forgetting = min(1, $daysSince / 7);

        $score = $errorRate + (0.5 * $forgetting);
        $score -= ((int) $state['scheduled_count']) * 0.05;

        return $score;
    }

    /**
     * Chooses the type of study session (theory, exercise, or revision) for a given topic.
     * Prioritizes based on completion status, remaining time, and recent session types.
     *
     * If the assunto has explicit allowed types (Assunto::tipo), this list is treated as the source of truth.
     *
     * @param  array<string, mixed>  $state  Current assunto scheduling state.
     * @param  int  $minutosRestantes  Remaining minutes in the current day.
     * @param  array<int, string>  $ultimosTipos  Recent scheduled types (used to reduce repetition).
     * @return string|null Chosen type key (teoria|exercicio|revisao) or null.
     *
     * @internal This method is for internal use within the service.
     */
    private function escolherTipo(array $state, int $minutosRestantes, array $ultimosTipos): ?string
    {
        $tiposRestritos = $state['assunto']?->tipo;
        if (is_string($tiposRestritos) && $tiposRestritos !== '') {
            $tiposRestritos = [$tiposRestritos];
        }

        // Se o assunto tem restrição explícita de tipos, ela deve ser a fonte de verdade.
        // (Compatível com o comportamento antigo de "tipo" único.)
        if (is_array($tiposRestritos) && ! empty($tiposRestritos)) {
            $permitidos = array_values(array_filter(
                $tiposRestritos,
                fn ($tipo) => is_string($tipo) && array_key_exists($tipo, self::SESSION_MINUTES)
            ));
        } else {
            $permitidos = $state['teoria_finalizada']
                ? ['exercicio', 'revisao']
                : ['teoria', 'revisao'];
        }

        $permitidos = array_values(array_filter(
            $permitidos,
            fn (string $tipo) => self::SESSION_MINUTES[$tipo] <= $minutosRestantes
        ));

        if (empty($permitidos)) {
            return null;
        }

        usort($permitidos, function (string $a, string $b) use ($state) {
            return ($state['type_counts'][$a] ?? 0) <=> ($state['type_counts'][$b] ?? 0);
        });

        $selecionado = $permitidos[0];

        if (count($ultimosTipos) >= 2) {
            $ultimo = $ultimosTipos[count($ultimosTipos) - 1];
            $penultimo = $ultimosTipos[count($ultimosTipos) - 2];

            if ($ultimo === $selecionado && $penultimo === $selecionado) {
                foreach ($permitidos as $alternativo) {
                    if ($alternativo !== $selecionado) {
                        $selecionado = $alternativo;
                        break;
                    }
                }
            }
        }

        return $selecionado;
    }
}
