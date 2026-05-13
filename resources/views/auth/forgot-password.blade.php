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

<body class="min-h-screen bg-main-light text-purple-night">
    <main class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-6xl grid gap-6 lg:gap-0 lg:grid-cols-[1.05fr_1.4fr]">
            <section
                class="bg-main-light rounded-[28px] lg:rounded-r-none p-10 sm:p-12 shadow-[0_20px_80px_rgba(64,36,92,0.08)]">
                <div class="max-w-md">
                    <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-purple-dark uppercase">Recuperar</h1>
                    <p class="mt-2 text-sm text-purple-muted">Esqueceu sua senha? Informe seu e-mail para receber um link de redefinição.</p>

                    <form class="mt-10 space-y-5" method="POST" action="/forgot-password">
                        @csrf

                        <x-validation-errors />

                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-secondary-green">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div>
                            <label for="email" class="sr-only">Email</label>
                            <x-input id="email" name="email" type="email" placeholder="Email"
                                value="{{ old('email') }}" required autofocus>
                                <x-slot name="icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z"
                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </x-slot>
                            </x-input>
                        </div>

                        <x-button>
                            Enviar
                        </x-button>

                        <div class="flex items-center gap-4 text-xs text-purple-muted">
                            <span class="h-px flex-1 bg-purple-light"></span>
                            <span>ou</span>
                            <span class="h-px flex-1 bg-purple-light"></span>
                        </div>

                        <p class="text-xs text-purple-muted">
                            Lembrou a senha?
                            <a href="{{ route('login') }}"
                                class="font-semibold text-purple-dark hover:text-purple-deep">Voltar ao login</a>
                        </p>
                    </form>
                </div>
            </section>

            <section
                class="bg-secondary-green rounded-[28px] lg:rounded-l-none p-10 sm:p-12 flex flex-col items-center justify-center text-center text-main-dark">
                <div class="w-full max-w-md">
                    <x-logo class="mb-6 w-48" />
                    <h2 class="text-3xl font-bold tracking-wide">TATU<span class="text-purple-dim">DO</span>ESTUDADO
                    </h2>
                    <p class="mt-3 text-sm font-semibold text-main-paper">ORGANIZE SEUS ESTUDOS</p>
                </div>
            </section>
        </div>
    </main>
</body>

</html>