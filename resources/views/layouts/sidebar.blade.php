<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TatuDoEstudado') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#E6E6E6] text-[#1F1F1F]">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-[#B9BBA2] text-[#1F1F1F] flex flex-col border-r border-[#9FA089]">
            <div class="px-6 pt-6">
                <div class="mx-auto w-40">
                    <img src="{{ Vite::asset('resources/assets/logo.png') }}" alt="Logo Tatu do Estudado"
                        class="w-full" />
                </div>
                <p class="mt-2 text-center font-rem text-[20px] font-bold leading-none text-[#1F1F1F]">
                    TATU<span class="text-[#7B6AA7]">DO</span>ESTUDADO
                </p>
            </div>

            <div class="my-4 h-px w-full bg-[#7B6AA7]"></div>

            <nav class="mt-8 flex-1 px-6 space-y-5">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-[#1F1F1F]">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path d="M7 8h10M7 12h10M7 16h10" stroke-linecap="round" />
                        </svg>
                    </span>
                    Cronograma
                </a>
                <a href="{{ route('materias.index') }}"
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-[#1F1F1F]">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                            <path d="M5 20h14M6 20V6a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14" stroke-linecap="round" />
                            <path d="M9 4h6" stroke-linecap="round" />
                        </svg>
                    </span>
                    Matérias
                </a>
            </nav>

            <div class="px-6 py-5 border-t border-[#9FA089]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-poppins text-[20px] font-medium leading-none text-[#1F1F1F]">
                            {{ auth()->user()->name ?? 'Usuário' }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-full border border-[#7B6AA7] px-4 py-2 text-sm font-semibold text-[#7B6AA7] hover:bg-[#7B6AA7] hover:text-white transition">
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
