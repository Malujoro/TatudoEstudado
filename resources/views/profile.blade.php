@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-purple-light text-purple-deep">
                    @if ($user->photo_url)
                        <img src="{{ $user->photo_url }}" alt="Foto de perfil de {{ $user->name }}"
                            class="h-full w-full object-cover" data-profile-avatar />
                    @else
                        <span class="font-rem text-[28px] font-bold leading-none" data-profile-initial>
                            {{ str($user->name)->trim()->upper()->substr(0, 1) }}
                        </span>
                    @endif
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

            <div class="flex items-center gap-3">
                <button type="button"
                    class="rounded-full bg-purple px-5 py-2.5 text-sm font-bold text-white hover:opacity-90 transition"
                    data-open-profile-modal>
                    Editar perfil
                </button>
            </div>
        </div>

        <x-validation-errors />

        @if (session('status'))
            <div class="rounded-2xl border border-purple-dim/30 bg-white/60 px-4 py-3 text-sm text-purple-night">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 w-fit">
            <p class="font-rem text-xs font-bold uppercase tracking-[0.18em] text-purple-muted">
                Defina seu tempo disponível por dia
            </p>

            <form action="{{ route('profile.update') }}" method="POST"
                class="rounded-3xl bg-purple-light px-5 py-5">
                @csrf

                <div class="flex flex-col gap-2" data-study-hours>

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
                'domingo' => 'Dom',
                'segunda' => 'Seg',
                'terca' => 'Ter',
                'quarta' => 'Qua',
                'quinta' => 'Qui',
                'sexta' => 'Sex',
                'sabado' => 'Sáb',
            ];
        @endphp

        <div class="flex flex-col gap-1.5">
            @foreach ($dias as $key => $label)
                @php
                    $valorDecimal = old('horario_semanal.' . $key, $horario[$key] ?? 0);
                    $minutosTotais = (int) round($valorDecimal * 60);
                    $horas = intdiv($minutosTotais, 60);
                    $minutos = $minutosTotais % 60;
                @endphp
                <div class="inline-flex items-center gap-2 rounded-2xl bg-white/40 px-4 py-2.5" data-day-row>
                    
                    <span class="w-8 font-rem text-xs font-bold uppercase tracking-wide text-purple-night">{{ $label }}</span>
                    <div class="flex items-center gap-1">
                        <input type="number" min="0" max="24" inputmode="numeric"
                            placeholder="0" value="{{ $horas }}" data-hours-input
                            style="width: 3rem;"
                            class="rounded-xl border border-purple-dim/30 bg-white px-2 py-2 text-center text-sm text-purple-night outline-none focus:border-purple focus:ring-1 focus:ring-purple" />
                        <span class="text-xs font-semibold text-purple-night/70">h</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <select data-minutes-input
                            class="w-16 rounded-xl border border-purple-dim/30 bg-white px-2 py-2 text-sm text-purple-night outline-none focus:border-purple focus:ring-1 focus:ring-purple">
                            <option value="0" @selected($minutos == 0)>00</option>
                            <option value="15" @selected($minutos == 15)>15</option>
                            <option value="30" @selected($minutos == 30)>30</option>
                            <option value="45" @selected($minutos == 45)>45</option>
                        </select>
                        <span class="text-xs font-semibold text-purple-night/70">m</span>
                    </div>
                    <input type="hidden" name="horario_semanal[{{ $key }}]"
                        value="{{ $valorDecimal }}" data-hidden-input />
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between rounded-2xl bg-white/40 px-4 py-2.5">
            <p class="text-xs text-purple-night/70">
                Total: <span class="font-bold text-purple-night" data-week-total>0h</span>
            </p>
            <p class="text-xs text-purple-night/70">
                Média/dia: <span class="font-bold text-purple-night" data-day-avg>0h</span>
            </p>
        </div>
    </div>

                <div class="mt-4">
                    <button type="submit"
                        class="w-full rounded-full bg-purple px-6 py-2.5 text-sm font-bold text-white hover:opacity-90 transition">
                        Salvar horários
                    </button>
                </div>
            </form>
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

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" data-profile-modal>
        <div class="flex w-full max-w-2xl flex-col rounded-3xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="font-rem text-xl font-bold text-purple-night">Editar perfil</h3>
                    <p class="text-sm text-purple-night/70">Atualize nome e foto de perfil.</p>
                </div>
                <button type="button" class="text-purple-night" data-close-profile-modal>✕</button>
            </div>

            <form action="{{ route('profile.details.update') }}" method="POST" enctype="multipart/form-data"
                class="mt-6 flex flex-col gap-5">
                @csrf

                <div class="flex flex-col items-center gap-3">
                    <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-purple-light text-purple-deep shadow-sm ring-4 ring-purple-light/30">
                        @if ($user->photo_url)
                            <img src="{{ $user->photo_url }}" alt="Foto de perfil de {{ $user->name }}"
                                class="h-full w-full object-cover" />
                        @else
                            <span class="font-rem text-[34px] font-bold leading-none">
                                {{ str($user->name)->trim()->upper()->substr(0, 1) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                            class="rounded-full border border-purple-dim px-4 py-2 text-xs font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition"
                            data-modal-remove-photo-button>
                            Remover foto
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 text-sm font-semibold text-purple-night">
                        Nome
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="rounded-2xl border border-purple-dim/30 bg-white px-4 py-3 text-sm text-purple-night outline-none focus:border-purple focus:ring-1 focus:ring-purple" />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-semibold text-purple-night">
                        Email
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" readonly
                            class="cursor-not-allowed rounded-2xl border border-purple-dim/30 bg-white/80 px-4 py-3 text-sm text-purple-night outline-none" />
                    </label>
                </div>

                    <div class="flex flex-col gap-3">
                    <label class="block text-xs font-bold uppercase tracking-[0.18em] text-purple-muted">
                        Foto de perfil
                    </label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input type="file" name="imagem" accept="image/*"
                            class="w-full rounded-2xl border border-purple-dim/30 bg-white px-4 py-3 text-sm text-purple-night file:mr-4 file:rounded-full file:border-0 file:bg-purple-light file:px-4 file:py-2 file:text-sm file:font-semibold file:text-purple-night"
                            data-modal-photo-input />
                    </div>
                    <input type="hidden" name="remove_photo" value="0" data-modal-remove-photo-input />

                    <div class="hidden rounded-2xl border border-dashed border-purple-dim/40 bg-white/60 p-3"
                        data-modal-photo-preview-wrap>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-purple-muted">Prévia</p>
                        <img src="{{ $user->photo_url }}" alt="Prévia da foto de perfil"
                            class="h-56 w-full rounded-3xl object-cover sm:h-72" data-modal-photo-preview />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button"
                        class="rounded-full border border-purple-dim px-5 py-2.5 text-sm font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition"
                        data-close-profile-modal>
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-full bg-purple px-5 py-2.5 text-sm font-bold text-white hover:opacity-90 transition">
                        Salvar dados
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const root = document.querySelector('[data-study-hours]');
            if (!root) return;

            const dayRows = Array.from(root.querySelectorAll('[data-day-row]'));
            const totalEl = root.querySelector('[data-week-total]');
            const avgEl = root.querySelector('[data-day-avg]');

            const formatTime = (decimalHours) => {
                const totalMins = Math.round(decimalHours * 60);
                const h = Math.floor(totalMins / 60);
                const m = totalMins % 60;
                return m > 0 ? `${h}h${m}m` : `${h}h`;
            };

            const updateSummary = () => {
                let weekTotal = 0;

                dayRows.forEach(row => {
                    const hoursInput = row.querySelector('[data-hours-input]');
                    const minutesInput = row.querySelector('[data-minutes-input]');
                    const hiddenInput = row.querySelector('[data-hidden-input]');

                    const hours = parseInt(hoursInput.value) || 0;
                    const minutes = parseInt(minutesInput.value) || 0;

                    const totalDecimal = hours + (minutes / 60);

                    if (hiddenInput) {
                        hiddenInput.value = totalDecimal;
                    }

                    weekTotal += totalDecimal;
                });

                const dayAvg = weekTotal / 7;

                if (totalEl) totalEl.textContent = formatTime(weekTotal);
                if (avgEl) avgEl.textContent = formatTime(dayAvg);
            };

            updateSummary();

            dayRows.forEach(row => {
                const hoursInput = row.querySelector('[data-hours-input]');
                const minutesInput = row.querySelector('[data-minutes-input]');

                if (hoursInput) hoursInput.addEventListener('input', updateSummary);
                if (minutesInput) minutesInput.addEventListener('input', updateSummary);
            });
        })();

        (function() {
            const modal = document.querySelector('[data-profile-modal]');
            const openButton = document.querySelector('[data-open-profile-modal]');
            const closeButtons = document.querySelectorAll('[data-close-profile-modal]');
            const modalFileInput = document.querySelector('[data-modal-photo-input]');
            const modalPreviewWrap = document.querySelector('[data-modal-photo-preview-wrap]');
            const modalPreviewImg = document.querySelector('[data-modal-photo-preview]');
            const modalRemoveButton = document.querySelector('[data-modal-remove-photo-button]');
            const modalRemoveInput = document.querySelector('[data-modal-remove-photo-input]');

            const openModal = () => {
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
            };

            const closeModal = () => {
                modal?.classList.add('hidden');
                modal?.classList.remove('flex');
            };

            openButton?.addEventListener('click', openModal);
            closeButtons.forEach((button) => button.addEventListener('click', closeModal));

            modalFileInput?.addEventListener('change', () => {
                const file = modalFileInput.files?.[0];
                if (!file || !modalPreviewWrap || !modalPreviewImg || !modalRemoveInput) return;

                modalRemoveInput.value = '0';

                const reader = new FileReader();
                reader.onload = (event) => {
                    const result = event.target?.result;
                    if (typeof result === 'string') {
                        modalPreviewImg.src = result;
                        modalPreviewWrap.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            });

            modalRemoveButton?.addEventListener('click', () => {
                if (modalFileInput) modalFileInput.value = '';
                if (modalRemoveInput) modalRemoveInput.value = '1';
                if (modalPreviewWrap && modalPreviewImg) {
                    modalPreviewImg.src = '';
                    modalPreviewWrap.classList.remove('hidden');
                }
            });
        })();
    </script>
@endsection
