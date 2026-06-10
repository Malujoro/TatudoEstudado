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

<body class="min-h-screen bg-main-light text-main-dark">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-secondary-green text-main-dark flex flex-col border-r border-secondary-green">
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

        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

            <script>
                // Global helper to prompt the user to generate a new cronograma using SweetAlert2
                window.promptGerarCronograma = async function(title = 'Disponibilidade alterada!') {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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

                    try {
                        await fetch('/api/cronograma/gerar', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        return true;
                    } catch (e) {
                        console.error('Erro ao gerar cronograma:', e);
                        return false;
                    }
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
