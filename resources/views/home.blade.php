@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="sticky top-0 z-20 -mx-8 px-8 pt-8 pb-4 bg-main-light border-b border-purple-dim/10 flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <h2 class="font-rem text-[22px] font-bold text-main-dark">Metas da semana</h2>
                    <x-button type="button" data-generate-cronograma>
                        Gerar metas da semana
                        <span
                            class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/70 text-purple-dark">
                            <x-icons.calendar class="w-4 h-4" />
                        </span>
                    </x-button>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home', ['week' => $weekOffset - 1]) }}"
                        class="rounded-full border border-purple-dim px-4 py-2 text-sm font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition">
                        Semana anterior
                    </a>
                    <span class="rounded-full bg-white/70 px-4 py-2 text-sm font-semibold text-purple-night">
                        {{ $inicioSemana->format('d/m') }} - {{ $fimSemana->format('d/m') }}
                    </span>
                    <a href="{{ route('home', ['week' => $weekOffset + 1]) }}"
                        class="rounded-full border border-purple-dim px-4 py-2 text-sm font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition">
                        Próxima semana
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-tag tipo="teoria" />
                <x-tag tipo="exercicio" />
                <x-tag tipo="revisao" />
            </div>
        </div>

        @php
            $temSessoes = collect($dias)->contains(fn($dia) => $dia['sessoes']->isNotEmpty());
        @endphp

        @if (!$temSessoes)
            <div class="rounded-3xl border-2 border-dashed border-purple bg-main-light px-8 py-20 text-center">
                <p class="mx-auto max-w-3xl font-rem text-[22px] font-medium leading-snug text-main-dark">
                    Você ainda não criou um cronograma, defina seu horário disponível no perfil e clique em Gerar Cronograma
                </p>
            </div>
        @else
            <div class="flex flex-col gap-6">
                @foreach ($dias as $dia)
                    @php
                        $ehHoje = \Carbon\Carbon::parse($dia['data'])->isToday();
                    @endphp

                    <div
                        class="rounded-[26px] border-2 border-purple bg-purple-lightest px-6 py-4 shadow-sm"
                        @if($ehHoje) data-dia-atual @endif
                    >
                        <div class="flex flex-col gap-4">
                            <div>
                                <span
                                    class="inline-flex items-center justify-center rounded-2xl bg-purple-light px-4 py-2 font-rem text-sm font-bold text-purple-night">
                                    {{ $dia['label'] }}
                                </span>
                            </div>

                            <div class="flex flex-1 flex-wrap gap-4">
                                @forelse ($dia['sessoes'] as $sessao)
                                    @php
                                        $minutos = (int) round($sessao->horas * 60);
                                        $horas = intdiv($minutos, 60);
                                        $resto = $minutos % 60;
                                        $duracao =
                                            $horas > 0
                                                ? ($resto > 0
                                                    ? $horas . 'h' . $resto
                                                    : $horas . 'h')
                                                : $resto . 'min';
                                        $tipo = $sessao->tipo;
                                    @endphp

                                    <div
                                        class="flex min-w-[190px] flex-col gap-2 rounded-2xl px-5 py-4 tema-{{ $tipo ?: 'default' }} {{ $sessao->finalizado ? 'opacity-70' : '' }}">
                                        <p class="font-rem text-xs font-bold uppercase tracking-[0.2em]">
                                            {{ strtoupper($sessao->assunto->materia->nome ?? 'Matéria') }}
                                        </p>
                                        <p
                                            class="font-rem text-sm font-semibold {{ $sessao->finalizado ? 'line-through' : '' }}">
                                            {{ $sessao->assunto->nome ?? 'Assunto' }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="rounded-full bg-white/70 px-3 py-1 text-[11px] font-semibold text-purple-night">
                                                {{ $duracao }}
                                            </span>
                                            @if ($sessao->finalizado)
                                                <span
                                                    class="rounded-full bg-white/70 px-3 py-1 text-[10px] font-semibold text-purple-night">
                                                    Concluída
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <span class="text-sm text-purple-light">
                                        Sem sessões
                                    </span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50" data-modal-caderno>
        <div class="w-full max-w-xl rounded-3xl bg-white p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-rem text-lg font-semibold text-purple-night">Caderno de erros</h3>
                <button type="button" class="text-purple-night" data-close-modal-caderno>✕</button>
            </div>
            <textarea class="mt-4 w-full rounded-2xl border border-purple-dim/50 bg-white px-4 py-3 text-sm text-purple-night"
                rows="6" data-caderno-texto></textarea>
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
            const button = document.querySelector('[data-generate-cronograma]');
            if (!button) return;

            button.addEventListener('click', async () => {
                button.setAttribute('disabled', 'disabled');
                button.classList.add('opacity-70');

                if (typeof window.executarGeracaoCronograma === 'function') {
                    const success = await window.executarGeracaoCronograma();
                    if (success) {
                        window.location.reload();
                    } else {
                        button.removeAttribute('disabled');
                        button.classList.remove('opacity-70');
                    }
                } else {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        const response = await fetch('/api/cronograma/gerar', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token || '',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Falha ao gerar cronograma');
                        }

                        window.location.reload();
                    } catch (error) {
                        console.error(error);
                        button.removeAttribute('disabled');
                        button.classList.remove('opacity-70');
                    }
                }
            });
        })();
    </script>

<div id="home-container-tatu" class="fixed z-50 pointer-events-none transition-all duration-700" style="bottom: 0; left: 0;">
        <video
            id="home-vid-animacao"
            autoplay
            muted
            playsinline
            class="w-32 md:w-80 mix-blend-multiply">
            <source src="{{ asset('videos/tatu_aponta.webm') }}" type="video/webm">
        </video>
    </div>

    <script>
        (function () {
            const vid = document.getElementById('home-vid-animacao');
            const container = document.getElementById('home-container-tatu');
            const delayMs = 3000; // delay em ms entre repetições

            vid.addEventListener('ended', () => {
                setTimeout(() => {
                    vid.currentTime = 0;
                    vid.play();
                }, delayMs);
            });

            function posicionarTatu() {
                const cardHoje = document.querySelector('[data-dia-atual]');
                if (!cardHoje) {
                    container.style.display = 'none'; // Esconde se não achar o card
                    return;
                }

                container.style.display = 'block'; // Garante que está visível

                const rectCard = cardHoje.getBoundingClientRect();
                const rectTatu = container.getBoundingClientRect();

                // Alinha verticalmente ao centro do card
                const top = (rectCard.top + (rectCard.height / 2) - (rectTatu.height / 2)) - 63;

                // Posiciona o tatu à esquerda do card (fora dele)
                const left = Math.max(8, rectCard.left - rectTatu.width) + 157;

                container.style.top = `${top}px`;
                container.style.left = `${left}px`;
                container.style.bottom = 'auto';
            }
            
            // Usa DOMContentLoaded para garantir que o HTML foi carregado antes de executar
            document.addEventListener('DOMContentLoaded', posicionarTatu);
            window.addEventListener('resize', posicionarTatu);
            window.addEventListener('scroll', posicionarTatu);
        })();
    </script>
    
@endsection
