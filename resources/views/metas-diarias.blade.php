@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <h2 class="font-rem text-[22px] font-bold text-main-dark">Metas diárias</h2>
                
                @php
                    $sequencia = auth()->user()->sequencia_estudo;
                @endphp
                
                @if($sequencia > 0)
                    <!-- Active Streak Badge -->
                    <div class="flex items-center gap-2 rounded-2xl bg-amber-50 border border-amber-200/60 px-3.5 py-1.5 text-amber-800 shadow-sm transition-all hover:scale-105 duration-300 group" title="Sua sequência de estudos está ativa!">
                        <x-icons.fire class="w-5 h-5 text-orange-500 fill-orange-500 transition-transform duration-300 group-hover:scale-110" />
                        <span class="font-rem text-sm font-bold tracking-tight">
                            {{ $sequencia }} {{ $sequencia == 1 ? 'dia seguido' : 'dias seguidos' }}!
                        </span>
                    </div>
                @else
                    <!-- Inactive Streak Badge (Grayed out to stimulate user) -->
                    <div class="flex items-center gap-2 rounded-2xl bg-gray-50 border border-gray-200/50 px-3.5 py-1.5 text-gray-400 shadow-sm transition-all hover:scale-105 duration-300 group" title="Complete uma meta hoje para começar sua sequência!">
                        <x-icons.fire class="w-5 h-5 text-gray-300 fill-gray-300 opacity-60 transition-transform duration-300 group-hover:scale-110" />
                        <span class="font-rem text-sm font-semibold tracking-tight">
                            Comece sua sequência!
                        </span>
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <x-tag tipo="teoria" />
                <x-tag tipo="exercicio" />
                <x-tag tipo="revisao" />
            </div>
        </div>

        @if ($diasPendentes->isEmpty())
            <div class="rounded-3xl border border-purple-dim/40 bg-white/70 px-6 py-8 text-center">
                <p class="font-rem text-sm font-medium text-purple-night">
                    Você está em dia com suas metas de estudo.
                </p>
            </div>
        @else
            <div class="flex flex-col gap-6">
                @foreach ($diasPendentes as $dia)
                    <div class="rounded-[26px] border-2 border-purple bg-purple-lightest px-6 py-4 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center justify-center rounded-2xl bg-purple-light px-4 py-2 font-rem text-sm font-bold text-purple-night">
                                    {{ $dia['label'] }}
                                </span>
                                <span class="text-sm font-semibold text-purple-night">
                                    {{ \Illuminate\Support\Carbon::parse($dia['data'])->format('d/m') }}
                                </span>
                            </div>

                            @if ($dia['atrasada'])
                                <span class="rounded-full bg-secondary-red/80 px-3 py-1 text-xs font-semibold text-white">
                                    Meta atrasada
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-col gap-4">
                            @foreach ($dia['sessoes'] as $sessao)
                                @php
                                    $metrica = $sessao->assunto->metrica;
                                    $acertos = (int) ($metrica->acertos ?? 0);
                                    $erros = (int) ($metrica->erros ?? 0);
                                    $total = $acertos + $erros;
                                    $media = $total > 0 ? round(($acertos / $total) * 100) : 0;
                                    $tipo = $sessao->tipo;
                                @endphp

                                <div class="rounded-2xl border border-purple-dim/40 bg-white px-5 py-4 {{ $sessao->finalizado ? 'opacity-60' : '' }}"
                                    data-session-card data-session-id="{{ $sessao->id }}">
                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <div class="flex flex-col gap-1">
                                                <p
                                                    class="text-xs font-semibold uppercase tracking-[0.18em] text-purple-muted">
                                                    {{ $sessao->assunto->materia->nome ?? 'Matéria' }}
                                                </p>
                                                <p class="font-rem text-base font-semibold text-purple-night {{ $sessao->finalizado ? 'line-through' : '' }}"
                                                    data-session-title>
                                                    {{ $sessao->assunto->nome ?? 'Assunto' }}
                                                </p>
                                                <p class="text-xs text-purple-night/70">
                                                    Média de acerto: <span class="font-semibold">{{ $media }}%</span>
                                                </p>
                                            <div class="mt-2 flex">
                                                <span
                                                    class="rounded-full px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider tema-{{ $tipo ?: 'default' }}">
                                                    {{ $tipo }}
                                                </span>
                                            </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2" data-session-actions>
                                            <button type="button"
                                                class="rounded-full border border-purple-dim px-4 py-2 text-xs font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition"
                                                data-open-caderno data-assunto-id="{{ $sessao->assunto_id }}"
                                                data-caderno-id="{{ $sessao->assunto->caderno->id ?? '' }}"
                                                data-caderno-conteudo="{{ $sessao->assunto->caderno->conteudo ?? '' }}">
                                                Caderno de erros
                                            </button>

                                            <button type="button"
                                                class="rounded-full bg-purple-light px-4 py-2 text-xs font-semibold text-purple-night hover:opacity-80 transition"
                                                data-finalizar data-sessao-id="{{ $sessao->id }}"
                                                data-tipo="{{ $sessao->tipo }}"
                                                data-assunto-id="{{ $sessao->assunto_id }}">
                                                Finalizar meta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3" data-modal-caderno>
        <div class="flex h-[94vh] w-full max-w-6xl flex-col rounded-3xl bg-white p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-rem text-lg font-semibold text-purple-night">Caderno de erros</h3>
                <button type="button" class="text-purple-night" data-close-modal-caderno>✕</button>
            </div>
            <textarea
                class="mt-4 min-h-0 flex-1 w-full rounded-2xl border border-purple-dim/50 bg-white px-4 py-3 text-sm text-purple-night"
                data-caderno-texto></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button"
                    class="rounded-full border border-purple-dim px-4 py-2 text-xs font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition"
                    data-close-modal-caderno>Cancelar</button>
                <button type="button"
                    class="rounded-full bg-purple-light px-4 py-2 text-xs font-semibold text-purple-night hover:opacity-80 transition"
                    data-save-caderno>Salvar</button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50" data-modal-exercicio>
        <div class="w-full max-w-md rounded-3xl bg-white p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-rem text-lg font-semibold text-purple-night">Finalizar exercício</h3>
                <button type="button" class="text-purple-night" data-close-modal-exercicio>✕</button>
            </div>
            <div class="mt-4 flex flex-col gap-3">
                <label class="text-sm font-semibold text-purple-night">
                    Quantas questões você fez?
                    <input type="number" min="0"
                        class="mt-1 w-full rounded-2xl border border-purple-dim/50 px-4 py-2 text-sm" data-questoes />
                </label>
                <label class="text-sm font-semibold text-purple-night">
                    Quantas você acertou?
                    <input type="number" min="0"
                        class="mt-1 w-full rounded-2xl border border-purple-dim/50 px-4 py-2 text-sm" data-acertos />
                </label>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button"
                    class="rounded-full border border-purple-dim px-4 py-2 text-xs font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition"
                    data-close-modal-exercicio>Cancelar</button>
                <button type="button"
                    class="rounded-full bg-purple-light px-4 py-2 text-xs font-semibold text-purple-night hover:opacity-80 transition"
                    data-save-exercicio>Salvar</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const modalCaderno = document.querySelector('[data-modal-caderno]');
            const textoCaderno = document.querySelector('[data-caderno-texto]');
            const saveCaderno = document.querySelector('[data-save-caderno]');
            const closeCaderno = document.querySelectorAll('[data-close-modal-caderno]');

            let cadernoState = {
                assuntoId: null,
                cadernoId: null
            };

            document.querySelectorAll('[data-open-caderno]').forEach((button) => {
                button.addEventListener('click', () => {
                    cadernoState = {
                        assuntoId: button.getAttribute('data-assunto-id'),
                        cadernoId: button.getAttribute('data-caderno-id') || null,
                    };

                    if (textoCaderno) {
                        textoCaderno.value = button.getAttribute('data-caderno-conteudo') || '';
                    }

                    if (modalCaderno) {
                        modalCaderno.classList.remove('hidden');
                        modalCaderno.classList.add('flex');
                    }
                });
            });

            closeCaderno.forEach((button) => {
                button.addEventListener('click', () => {
                    modalCaderno?.classList.add('hidden');
                    modalCaderno?.classList.remove('flex');
                });
            });

            saveCaderno?.addEventListener('click', async () => {
                if (!cadernoState.assuntoId) return;

                const conteudo = textoCaderno?.value || '';
                const isUpdate = Boolean(cadernoState.cadernoId);
                const url = isUpdate ?
                    `/api/cadernos/${cadernoState.cadernoId}` :
                    '/api/cadernos';
                const method = isUpdate ? 'PUT' : 'POST';

                const payload = isUpdate ? {
                    conteudo
                } : {
                    conteudo,
                    assunto_id: cadernoState.assuntoId
                };

                const response = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                if (response.ok) {
                    window.location.reload();
                    return;
                }
            });

            const modalExercicio = document.querySelector('[data-modal-exercicio]');
            const inputQuestoes = document.querySelector('[data-questoes]');
            const inputAcertos = document.querySelector('[data-acertos]');
            const saveExercicio = document.querySelector('[data-save-exercicio]');
            const closeExercicio = document.querySelectorAll('[data-close-modal-exercicio]');
            let sessaoAtual = null;

            document.querySelectorAll('[data-finalizar]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const tipo = button.getAttribute('data-tipo');
                    const sessaoId = button.getAttribute('data-sessao-id');
                    if (!sessaoId) return;

                    if (tipo === 'exercicio') {
                        sessaoAtual = sessaoId;
                        if (inputQuestoes) inputQuestoes.value = '';
                        if (inputAcertos) inputAcertos.value = '';
                        modalExercicio?.classList.remove('hidden');
                        modalExercicio?.classList.add('flex');
                        return;
                    }

                    await finalizarSessao(sessaoId, {});
                });
            });

            closeExercicio.forEach((button) => {
                button.addEventListener('click', () => {
                    modalExercicio?.classList.add('hidden');
                    modalExercicio?.classList.remove('flex');
                });
            });

            saveExercicio?.addEventListener('click', async () => {
                if (!sessaoAtual) return;

                const questoes = Number(inputQuestoes?.value || 0);
                const acertos = Number(inputAcertos?.value || 0);

                if (Number.isNaN(questoes) || Number.isNaN(acertos)) {
                    return;
                }

                await finalizarSessao(sessaoAtual, {
                    questoes,
                    acertos
                });
            });

            async function finalizarSessao(sessaoId, payload) {
                const response = await fetch(`/api/sessoes-estudo/${sessaoId}/finalizar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                if (response.ok) {
                    window.location.reload();
                }
            }
        })();
    </script>
@endsection
