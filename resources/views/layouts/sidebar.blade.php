<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TatuDoEstudado') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .swal2-container.swal2-backdrop-show {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
    </style>
</head>

<body class="h-screen bg-main-light text-main-dark">
    <div class="h-screen flex">
        <aside class="w-64 bg-secondary-green text-main-dark flex flex-col border-r border-secondary-green h-screen overflow-y-auto">
            <div class="px-6 pt-6">
                <div class="mx-auto w-40">
                    <img src="{{ Vite::asset('resources/assets/logo.png') }}" alt="Logo Tatu do Estudado"
                        class="w-full" />
                </div>
                <p class="mt-2 text-center font-rem text-[20px] font-bold leading-none text-main-dark">
                    TATU<span class="text-purple-dim">DO</span>ESTUDADO
                </p>
            </div>

            <div class="my-4 h-px w-full bg-purple-dim"></div>

            <nav class="mt-8 flex-1 px-6 space-y-5">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-main-dark">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path d="M7 8h10M7 12h10M7 16h10" stroke-linecap="round" />
                        </svg>
                    </span>
                    Metas da semana
                </a>
                <a href="{{ route('metas.diarias') }}"
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-main-dark">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path d="M8 7h8M8 12h8M8 17h8" stroke-linecap="round" />
                        </svg>
                    </span>
                    Metas diárias
                </a>
                <a href="{{ route('ranking') }}"
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-main-dark">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
                            <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
                            <path d="M4 22h16" />
                            <path d="M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34" />
                            <path d="M12 2a5 5 0 0 0-5 5v5a5 5 0 0 0 10 0V7a5 5 0 0 0-5-5z" />
                        </svg>
                    </span>
                    Ranking
                </a>
                <a href="{{ route('materias.index') }}"
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-main-dark">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path d="M5 20h14M6 20V6a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14" stroke-linecap="round" />
                            <path d="M9 4h6" stroke-linecap="round" />
                        </svg>
                    </span>
                    Matérias
                </a>
            </nav>

            <div class="px-6 py-5 border-t border-secondary-green">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 group">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white/60 text-purple-deep">
                            @if (auth()->user()?->photo_url)
                                <img src="{{ auth()->user()->photo_url }}" alt="Foto de perfil"
                                    class="h-full w-full object-cover" />
                            @else
                                <span class="font-rem text-base font-bold leading-none">
                                    {{ str(auth()->user()->name ?? 'U')->trim()->upper()->substr(0, 1) }}
                                </span>
                            @endif
                        </div>
                        <span
                            class="font-poppins text-[20px] font-medium leading-none text-main-dark group-hover:underline">
                            {{ auth()->user()->name ?? 'Usuário' }}
                        </span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-full border border-purple-dim px-4 py-2 text-sm font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-8 h-screen overflow-y-auto">
            @yield('content')
        </main>
    </div>

            <script>
                // Global helper to perform the schedule generation API call with checks and notifications
                window.executarGeracaoCronograma = async function() {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const callApi = async (ignorar) => {
                        const response = await fetch('/api/cronograma/gerar', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token || '',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ ignorar_zerado: ignorar })
                        });
                        if (response.ok) {
                            return { success: true };
                        }
                        if (response.status === 422) {
                            const data = await response.json();
                            return { success: false, error: data.error, message: data.message };
                        }
                        return { success: false, error: 'generic', message: 'Erro ao gerar o cronograma.' };
                    };

                    let result = await callApi(false);

                    if (result.success) {
                        return true;
                    }

                    if (result.error === 'sem_horas') {
                        await Swal.fire({
                            title: 'Aviso',
                            text: result.message || 'Não é possível gerar cronograma. Você não definiu suas horas disponíveis.',
                            icon: 'warning',
                            confirmButtonColor: 'var(--color-swal-confirm)',
                            confirmButtonText: 'Ok'
                        });
                        return false;
                    }

                    if (result.error === 'dia_zerado') {
                        const confirmResult = await Swal.fire({
                            title: 'Atenção',
                            text: result.message || 'Você tem ao menos um dia com tempo zerado. Deseja gerar o cronograma mesmo assim?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--color-swal-confirm)',
                            cancelButtonColor: 'var(--color-swal-cancel)',
                            confirmButtonText: 'Sim, gerar',
                            cancelButtonText: 'Não, cancelar'
                        });

                        if (confirmResult.isConfirmed) {
                            let secondResult = await callApi(true);
                            if (secondResult.success) {
                                return true;
                            } else {
                                await Swal.fire({
                                    title: 'Erro',
                                    text: secondResult.message || 'Falha ao gerar cronograma.',
                                    icon: 'error',
                                    confirmButtonColor: 'var(--color-swal-confirm)',
                                });
                            }
                        }
                        return false;
                    }

                    // Default generic error
                    await Swal.fire({
                        title: 'Erro',
                        text: result.message || 'Falha ao gerar cronograma.',
                        icon: 'error',
                        confirmButtonColor: 'var(--color-swal-confirm)',
                    });
                    return false;
                };

                // Global helper to prompt the user to generate a new cronograma using SweetAlert2
                window.promptGerarCronograma = async function(title = 'Disponibilidade alterada!') {
                    const result = await Swal.fire({
                        title: title,
                        text: 'Deseja gerar um novo cronograma para aplicar as mudanças?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--color-swal-confirm)',
                        cancelButtonColor: 'var(--color-swal-cancel)',
                        confirmButtonText: 'Sim, gerar novo',
                        cancelButtonText: 'Não, manter atual'
                    });

                    if (!result.isConfirmed) return false;

                    return await window.executarGeracaoCronograma();
                };

                // Legacy: if server flashed prompt_cronograma, trigger the prompt on load
                @if (session('prompt_cronograma'))
                    document.addEventListener('DOMContentLoaded', () => {
                        window.promptGerarCronograma();
                    });
                @endif
            </script>
</body>

</html>
