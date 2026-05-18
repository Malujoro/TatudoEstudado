@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center gap-4">
            <x-button type="button" data-generate-cronograma>
                Gerar Cronograma
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/70 text-purple-dark">
                    <x-icons.calendar class="w-4 h-4" />
                </span>
            </x-button>

            <div class="flex items-center gap-3">
                <x-tag tipo="teoria" />
                <x-tag tipo="exercicio" />
                <x-tag tipo="revisao" />
            </div>
        </div>

        @php
            $temSessoes = collect($dias)->contains(fn ($dia) => $dia['sessoes']->isNotEmpty());
            $coresTipo = [
                'teoria' => 'bg-purple-light text-purple-night',
                'exercicio' => 'bg-secondary-red text-main-dark',
                'revisao' => 'bg-secondary-blue text-main-dark',
            ];
        @endphp

        @if (! $temSessoes)
            <div class="rounded-3xl border-2 border-dashed border-purple bg-main-light px-8 py-20 text-center">
                <p class="mx-auto max-w-3xl font-rem text-[22px] font-medium leading-snug text-main-dark">
                    Você ainda não criou um cronograma, defina seu horário disponível no perfil e clique em Gerar Cronograma
                </p>
            </div>
        @else
            <div class="flex flex-col gap-6">
                @foreach ($dias as $dia)
                    <div class="rounded-[26px] border border-purple-dim/50 bg-main-dark/90 px-6 py-4">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center">
                            <span
                                class="inline-flex w-28 items-center justify-center rounded-2xl bg-purple-light px-4 py-2 font-rem text-sm font-bold text-purple-night">
                                {{ $dia['label'] }}
                            </span>

                            <div class="flex flex-1 flex-wrap gap-4">
                                @forelse ($dia['sessoes'] as $sessao)
                                    @php
                                        $minutos = (int) round($sessao->horas * 60);
                                        $horas = intdiv($minutos, 60);
                                        $resto = $minutos % 60;
                                        $duracao = $horas > 0
                                            ? ($resto > 0 ? $horas . 'h' . $resto : $horas . 'h')
                                            : $resto . 'min';
                                        $tipo = $sessao->tipo;
                                        $classe = $coresTipo[$tipo] ?? 'bg-purple-light text-purple-night';
                                    @endphp

                                    <div class="flex min-w-[190px] flex-col gap-2 rounded-2xl px-5 py-4 {{ $classe }}">
                                        <p class="font-rem text-xs font-bold uppercase tracking-[0.2em]">
                                            {{ strtoupper($sessao->assunto->materia->nome ?? 'Matéria') }}
                                        </p>
                                        <p class="font-rem text-sm font-semibold">
                                            {{ $sessao->assunto->nome ?? 'Assunto' }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="rounded-full bg-white/70 px-3 py-1 text-[11px] font-semibold text-purple-night">
                                                {{ $duracao }}
                                            </span>
                                            <span class="text-[11px] font-semibold uppercase tracking-wide">
                                                {{ $tipo }}
                                            </span>
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

    <script>
        (function() {
            const button = document.querySelector('[data-generate-cronograma]');
            if (!button) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            button.addEventListener('click', async () => {
                button.setAttribute('disabled', 'disabled');
                button.classList.add('opacity-70');

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
            });
        })();
    </script>
@endsection
