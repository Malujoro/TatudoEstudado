@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-purple-light text-purple-deep">
                    <span class="font-rem text-[28px] font-bold leading-none">
                        {{ str($user->name)->trim()->upper()->substr(0, 1) }}
                    </span>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="w-full max-w-md">
                        <x-input type="text" value="{{ $user->name }}" readonly />
                    </div>
                    <div class="w-full max-w-md">
                        <x-input type="email" value="{{ $user->email }}" readonly />
                    </div>
                </div>
            </div>
        </div>

        <x-validation-errors />

        @if (session('status'))
            <div class="rounded-2xl border border-purple-dim/30 bg-white/60 px-4 py-3 text-sm text-purple-night">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="flex flex-col gap-3">
                <p class="font-rem text-xs font-bold uppercase tracking-[0.18em] text-purple-muted">
                    Tempo de estudo por dia
                </p>

                <form action="{{ route('profile.update') }}" method="POST"
                    class="rounded-3xl bg-purple-light px-6 py-6">
                    @csrf

                    <div class="flex flex-col gap-4" data-study-hours>
                        <p class="font-rem text-sm font-medium text-purple-night">
                            Defina seu tempo disponível por dia
                        </p>

                        @php
                            $horario = $user->horario_semanal ?? [
                                'domingo' => 0,
                                'segunda' => 0,
                                'terca' => 0,
                                'quarta' => 0,
                                'quinta' => 0,
                                'sexta' => 0,
                                'sabado' => 0,
                            ];
                            $dias = [
                                'domingo' => 'Domingo',
                                'segunda' => 'Segunda',
                                'terca' => 'Terça',
                                'quarta' => 'Quarta',
                                'quinta' => 'Quinta',
                                'sexta' => 'Sexta',
                                'sabado' => 'Sábado',
                            ];
                        @endphp

                        <div class="grid grid-cols-1 gap-3">
                            @foreach ($dias as $key => $label)
                                <div class="grid grid-cols-[100px_1fr] items-center gap-3">
                                    <span class="font-rem text-sm font-medium text-purple-night">{{ $label }}</span>
                                    <x-input name="horario_semanal[{{ $key }}]" type="number" step="0.5" min="0" max="24"
                                        inputmode="decimal" placeholder="Ex: 2"
                                        value="{{ old('horario_semanal.' . $key, $horario[$key] ?? 0) }}" />
                                </div>
                            @endforeach
                        </div>

                        <div class="rounded-2xl bg-white/50 px-4 py-3">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-purple-night/70">
                                    Total na semana: <span class="font-semibold text-purple-night" data-week-total>0</span>h
                                </p>
                                <p class="text-xs text-purple-night/70">
                                    Média/dia (salva no sistema):
                                    <span class="font-semibold text-purple-night" data-day-avg>0</span>h
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <x-button type="submit">
                            Salvar
                        </x-button>
                    </div>
                </form>
            </div>

            <div class="flex flex-col gap-3">
                <p class="font-rem text-xs font-bold uppercase tracking-[0.18em] text-purple-muted">
                    Matérias
                </p>

                <div class="rounded-3xl bg-purple px-6 py-6">
                    <div class="flex flex-col gap-3">
                        @forelse($materias as $materia)
                            <div class="flex items-center justify-between rounded-full bg-white/40 px-4 py-2">
                                <span class="font-rem text-sm font-bold uppercase tracking-wide text-purple-deep">
                                    {{ $materia->nome }}
                                </span>
                                <span class="rounded-full bg-white/60 px-3 py-1 text-xs font-semibold text-purple-night">
                                    {{ $materia->assuntos_count }}
                                    {{ str('assunto')->plural($materia->assuntos_count) }}
                                </span>
                            </div>
                        @empty
                            <div class="rounded-full bg-white/30 px-4 py-2 text-center">
                                <span class="font-rem text-sm font-medium text-purple-night/70">
                                    Nenhuma matéria cadastrada
                                </span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const root = document.querySelector('[data-study-hours]');
            if (!root) return;

            const dayOrder = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];

            const inputs = Array.from(root.querySelectorAll('input[name^="horario_semanal"]'));
            const totalEl = root.querySelector('[data-week-total]');
            const avgEl = root.querySelector('[data-day-avg]');

            const toNumber = (value) => {
                if (value === null || value === undefined) return 0;
                const normalized = String(value).replace(',', '.');
                const number = Number(normalized);
                return Number.isFinite(number) ? number : 0;
            };

            const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

            const getCurrentStateFromInputs = () => {
                const state = {};
                for (const input of inputs) {
                    const match = input.name.match(/horario_semanal\[(\w+)\]/);
                    if (match) {
                        state[match[1]] = input.value;
                    }
                }
                return state;
            };

            const updateSummary = () => {
                const state = getCurrentStateFromInputs();
                let weekTotal = 0;

                for (const day of dayOrder) {
                    weekTotal += clamp(toNumber(state[day]), 0, 24);
                }

                const dayAvg = weekTotal / 7;

                if (totalEl) totalEl.textContent = (Math.round(weekTotal * 10) / 10).toString();
                if (avgEl) avgEl.textContent = (Math.round(dayAvg * 10) / 10).toString();
            };

            updateSummary();

            for (const input of inputs) {
                input.addEventListener('input', updateSummary);
            }
        })();
    </script>
@endsection