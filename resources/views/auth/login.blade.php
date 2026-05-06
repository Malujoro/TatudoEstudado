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
    <body class="min-h-screen bg-[#F1E8FA] text-[#3E2F4D]">
        <main class="min-h-screen flex items-center justify-center p-6">
            <div class="w-full max-w-6xl grid gap-6 lg:gap-0 lg:grid-cols-[1.05fr_1.4fr]">
                <section class="bg-[#EFE6FA] rounded-[28px] lg:rounded-r-none p-10 sm:p-12 shadow-[0_20px_80px_rgba(64,36,92,0.08)]">
                    <div class="max-w-md">
                        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-[#6C4A8F]">ENTRAR</h1>
                        <p class="mt-2 text-sm text-[#7C6A90]">Acesse sua conta para continuar</p>

                        <form class="mt-10 space-y-5" method="POST" action="{{ route('login.store') }}">
                            @csrf
                            @if ($errors->any())
                                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                                    <ul class="list-disc space-y-1 pl-4">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div>
                                <label for="email" class="sr-only">Email</label>
                                <div class="relative">
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        placeholder="Email"
                                        value="{{ old('email') }}"
                                        class="w-full rounded-full border border-[#3E2F4D] bg-white/80 px-4 py-3 text-sm text-[#4C3D5F] placeholder:text-[#8B7AA0] focus:outline-none focus:ring-2 focus:ring-[#A78AC9]"
                                    />
                                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#A78AC9]">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Z" stroke="currentColor" stroke-width="1.6"/>
                                            <path d="M4 20c0-3.314 3.582-6 8-6s8 2.686 8 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label for="password" class="sr-only">Senha</label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder="Senha"
                                        class="w-full rounded-full border border-[#3E2F4D] bg-white/80 px-4 py-3 text-sm text-[#4C3D5F] placeholder:text-[#8B7AA0] focus:outline-none focus:ring-2 focus:ring-[#A78AC9]"
                                    />
                                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#A78AC9]">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 12c2.7-4.2 5.7-6.3 9-6.3s6.3 2.1 9 6.3c-2.7 4.2-5.7 6.3-9 6.3S5.7 16.2 3 12Z" stroke="currentColor" stroke-width="1.6"/>
                                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                                        </svg>
                                    </span>
                                </div>
                                <a href="#" class="mt-2 block text-xs text-[#6C4A8F] hover:text-[#553574]">Esqueci minha senha</a>
                            </div>

                            <x-button>
                                Entrar
                            </x-button>

                            <div class="flex items-center gap-4 text-xs text-[#8B7AA0]">
                                <span class="h-px flex-1 bg-[#C7B7DD]"></span>
                                <span>ou</span>
                                <span class="h-px flex-1 bg-[#C7B7DD]"></span>
                            </div>

                            <p class="text-xs text-[#7C6A90]">
                                Não tem uma conta?
                                <a href="{{ route('register') }}" class="font-semibold text-[#6C4A8F] hover:text-[#553574]">Crie agora</a>
                            </p>
                        </form>
                    </div>
                </section>

                <section class="bg-[#B9BBA2] rounded-[28px] lg:rounded-l-none p-10 sm:p-12 flex flex-col items-center justify-center text-center text-[#2C2C2C]">
                    <div class="w-full max-w-md">
                        <x-logo class="mb-6 w-48" />
                        <h2 class="text-3xl font-bold tracking-wide">TATU<span class="text-[#7B6AA7]">DO</span>ESTUDADO</h2>
                        <p class="mt-3 text-sm font-semibold text-[#F6F4F1]">ORGANIZE SEUS ESTUDOS</p>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
