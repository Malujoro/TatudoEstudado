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
                    <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-purple-dark uppercase">Redefinir</h1>
                    <p class="mt-2 text-sm text-purple-muted">Crie uma nova senha segura para acessar sua conta.</p>

                    <form class="mt-10 space-y-5" method="POST" action="/reset-password">
                        @csrf

                        <input type="hidden" name="token" value="{{ request('token') }}">
                        <input type="hidden" name="email" value="{{ request('email') }}">

                        <x-validation-errors />

                        <div>
                            <label for="password" class="sr-only">Nova Senha</label>
                            <x-input id="password" name="password" type="password" placeholder="Nova senha" required
                                autofocus>
                                <x-slot name="icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3 12c2.7-4.2 5.7-6.3 9-6.3s6.3 2.1 9 6.3c-2.7 4.2-5.7 6.3-9 6.3S5.7 16.2 3 12Z"
                                            stroke="currentColor" stroke-width="1.6" />
                                        <circle cx="12" cy="12" r="3" stroke="currentColor"
                                            stroke-width="1.6" />
                                    </svg>
                                </x-slot>
                            </x-input>
                        </div>

                        <div>
                            <label for="password_confirmation" class="sr-only">Confirmar Senha</label>
                            <x-input id="password_confirmation" name="password_confirmation" type="password"
                                placeholder="Confirmar senha" required>
                                <x-slot name="icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M3 12c2.7-4.2 5.7-6.3 9-6.3s6.3 2.1 9 6.3c-2.7 4.2-5.7 6.3-9 6.3S5.7 16.2 3 12Z"
                                            stroke="currentColor" stroke-width="1.6" />
                                        <circle cx="12" cy="12" r="3" stroke="currentColor"
                                            stroke-width="1.6" />
                                    </svg>
                                </x-slot>
                            </x-input>
                        </div>

                        <x-button>
                            Redefinir senha
                        </x-button>
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