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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('profile') }}"
                            class="font-poppins text-[20px] font-medium leading-none text-main-dark hover:underline">
                            {{ auth()->user()->name ?? 'Usuário' }}
                        </a>
                    </div>
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
</body>

</html>
