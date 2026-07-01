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
                <button type="button" data-open-tutorial
                    class="flex items-center gap-3 font-rem text-[20px] font-medium leading-none text-main-dark hover:opacity-85 transition cursor-pointer w-full text-left">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                    Como funciona
                </button>
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

    <!-- Tutorial / Como funciona Modal -->
    <div id="tutorial-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div class="relative w-full max-w-2xl rounded-3xl bg-white p-8 shadow-2xl border border-purple-light/20 flex flex-col overflow-hidden max-h-[95vh] mx-4">
            <!-- Close button -->
            <button type="button" data-close-tutorial class="absolute top-6 right-6 text-purple-night/60 hover:text-purple-night text-xl transition-all cursor-pointer">✕</button>

            <!-- Tutorial Steps Container -->
            <div class="flex-1 flex flex-col justify-between">
                <!-- Step Content -->
                <div id="tutorial-steps-container" class="py-4">
                    <!-- Step 1 -->
                    <div class="tutorial-step flex flex-col gap-6" data-step="1">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-light text-purple-deep font-bold text-sm">1</span>
                            <h3 class="font-rem text-2xl font-bold text-purple-night">Cadastre suas Matérias</h3>
                        </div>
                        <p class="text-base text-purple-night/80 leading-relaxed font-sans">
                            O primeiro passo para organizar seus estudos é cadastrar as matérias que você precisa estudar (como Matemática, História, Biologia, etc.) e os assuntos específicos de cada uma.
                        </p>
                        <div class="rounded-2xl bg-purple-lightest p-5 border border-purple-light/20 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Acesse a aba <strong>Matérias</strong> no menu lateral.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Clique em <strong>Adicionar matéria</strong> para começar.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Adicione os assuntos que você deseja estudar dentro de cada matéria.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="tutorial-step hidden flex flex-col gap-6" data-step="2">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-light text-purple-deep font-bold text-sm">2</span>
                            <h3 class="font-rem text-2xl font-bold text-purple-night">Defina seus Horários</h3>
                        </div>
                        <p class="text-base text-purple-night/80 leading-relaxed font-sans">
                            Diga à plataforma quanto tempo você tem disponível para estudar em cada dia da semana. O TatudoEstudado usará essa informação para montar sua rotina ideal.
                        </p>
                        <div class="rounded-2xl bg-purple-lightest p-5 border border-purple-light/20 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Vá até o seu <strong>Perfil</strong> (clicando no seu nome no canto inferior esquerdo).</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">No painel <strong>Tempo de estudo por dia</strong>, insira as horas e minutos disponíveis.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Não se esqueça de clicar em <strong>Salvar horários</strong>.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="tutorial-step hidden flex flex-col gap-6" data-step="3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-light text-purple-deep font-bold text-sm">3</span>
                            <h3 class="font-rem text-2xl font-bold text-purple-night">Gere seu Cronograma</h3>
                        </div>
                        <p class="text-base text-purple-night/80 leading-relaxed font-sans">
                            Com as matérias e os horários cadastrados, nossa inteligência cria automaticamente o melhor planejamento semanal para você, equilibrando teoria, exercícios e revisões.
                        </p>
                        <div class="rounded-2xl bg-purple-lightest p-5 border border-purple-light/20 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Vá para a página inicial (<strong>Metas da semana</strong>).</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Clique no botão <strong>Gerar metas da semana</strong>.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Seu cronograma será montado dividindo seu tempo disponível entre os assuntos cadastrados.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="tutorial-step hidden flex flex-col gap-6" data-step="4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-light text-purple-deep font-bold text-sm">4</span>
                            <h3 class="font-rem text-2xl font-bold text-purple-night">Estude e Suba no Ranking!</h3>
                        </div>
                        <p class="text-base text-purple-night/80 leading-relaxed font-sans">
                            Acompanhe suas tarefas diárias, marque as sessões concluídas e registre seus acertos e erros de exercícios. Mantenha sua constância para subir no ranking!
                        </p>
                        <div class="rounded-2xl bg-purple-lightest p-5 border border-purple-light/20 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Acesse <strong>Metas diárias</strong> para ver o que estudar hoje.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Ao finalizar um exercício, registre as questões resolvidas e acertos.</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-purple shrink-0"></span>
                                <span class="text-sm font-semibold text-purple-night">Veja sua sequência de dias e concorra com outros no <strong>Ranking</strong>!</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dots Indicator & Buttons -->
                <div class="mt-6 pt-6 border-t border-purple-light/20 flex items-center justify-between">
                    <!-- Dots -->
                    <div class="flex gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-purple transition-all duration-300" data-dot="1"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-purple-light/40 transition-all duration-300" data-dot="2"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-purple-light/40 transition-all duration-300" data-dot="3"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-purple-light/40 transition-all duration-300" data-dot="4"></span>
                    </div>

                    <!-- Navigation buttons -->
                    <div class="flex gap-3">
                        <button type="button" id="btn-tutorial-prev" class="invisible rounded-full border border-purple-dim px-5 py-2.5 text-sm font-semibold text-purple-dim hover:bg-purple-dim hover:text-white transition cursor-pointer">Anterior</button>
                        <button type="button" id="btn-tutorial-next" class="rounded-full bg-purple px-6 py-2.5 text-sm font-bold text-white hover:opacity-90 transition cursor-pointer">Próximo</button>
                        <button type="button" id="btn-tutorial-finish" class="hidden rounded-full bg-purple px-6 py-2.5 text-sm font-bold text-white hover:opacity-90 transition cursor-pointer">Começar a Estudar!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de "Você não tem horas" -->
    <div id="no-hours-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl border border-purple-light/20 flex flex-col gap-6 relative mx-4">
            <button type="button" data-close-no-hours class="absolute top-6 right-6 text-purple-night/60 hover:text-purple-night text-xl transition-all cursor-pointer">✕</button>
            
            <div class="flex flex-col items-center text-center gap-4">
                <div class="h-16 w-16 rounded-full bg-secondary-red/10 text-secondary-red flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-8 w-8">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                
                <div class="flex flex-col gap-2">
                    <h3 class="font-rem text-xl font-bold text-purple-night">Você não tem horas cadastradas!</h3>
                    <p class="text-sm text-purple-night/70 leading-relaxed">
                        Para gerar o seu cronograma semanal, precisamos saber sua disponibilidade de estudo. Cadastre seus horários de estudo no perfil para continuar.
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-3 mt-2">
                <a href="{{ route('profile') }}" class="w-full text-center rounded-full bg-purple py-3 text-sm font-bold text-white hover:opacity-90 transition shadow-sm block cursor-pointer">
                    Configurar Horários no Perfil
                </a>
                <button type="button" data-close-no-hours class="w-full rounded-full border border-purple-dim/40 py-3 text-sm font-semibold text-purple-dim hover:bg-purple-lightest transition cursor-pointer">
                    Voltar
                </button>
            </div>
        </div>
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
                    document.addEventListener('turbo:load', () => {
                        window.promptGerarCronograma();
                    });
                @endif

                // --- TUTORIAL & NO-HOURS MODAL SCRIPTS ---
                document.addEventListener('turbo:load', () => {
                    // --- TUTORIAL MODAL LOGIC ---
                    const tutorialModal = document.getElementById('tutorial-modal');
                    const openTutorialBtn = document.querySelector('[data-open-tutorial]');
                    const closeTutorialBtns = document.querySelectorAll('[data-close-tutorial]');
                    const btnPrev = document.getElementById('btn-tutorial-prev');
                    const btnNext = document.getElementById('btn-tutorial-next');
                    const btnFinish = document.getElementById('btn-tutorial-finish');
                    
                    let currentStep = 1;
                    const totalSteps = 4;

                    const showStep = (step) => {
                        document.querySelectorAll('.tutorial-step').forEach(el => {
                            el.classList.add('hidden');
                        });
                        const activeStepEl = document.querySelector(`.tutorial-step[data-step="${step}"]`);
                        if (activeStepEl) activeStepEl.classList.remove('hidden');

                        // Update Dots
                        for (let i = 1; i <= totalSteps; i++) {
                            const dot = document.querySelector(`[data-dot="${i}"]`);
                            if (dot) {
                                if (i === step) {
                                    dot.classList.remove('bg-purple-light/40');
                                    dot.classList.add('bg-purple');
                                } else {
                                    dot.classList.add('bg-purple-light/40');
                                    dot.classList.remove('bg-purple');
                                }
                            }
                        }

                        // Prev Button visibility
                        if (step === 1) {
                            if (btnPrev) btnPrev.classList.add('invisible');
                        } else {
                            if (btnPrev) btnPrev.classList.remove('invisible');
                        }

                        // Next / Finish button toggle
                        if (step === totalSteps) {
                            if (btnNext) btnNext.classList.add('hidden');
                            if (btnFinish) btnFinish.classList.remove('hidden');
                        } else {
                            if (btnNext) btnNext.classList.remove('hidden');
                            if (btnFinish) btnFinish.classList.add('hidden');
                        }
                    };

                    const openTutorial = () => {
                        currentStep = 1;
                        showStep(currentStep);
                        if (tutorialModal) {
                            tutorialModal.classList.remove('hidden');
                            tutorialModal.classList.add('flex');
                        }
                    };

                    const closeTutorial = () => {
                        if (tutorialModal) {
                            tutorialModal.classList.add('hidden');
                            tutorialModal.classList.remove('flex');
                        }
                        localStorage.setItem('tatu_tutorial_seen', 'true');
                    };

                    if (openTutorialBtn) {
                        openTutorialBtn.addEventListener('click', openTutorial);
                    }

                    closeTutorialBtns.forEach(btn => {
                        btn.addEventListener('click', closeTutorial);
                    });

                    if (btnFinish) {
                        btnFinish.addEventListener('click', closeTutorial);
                    }

                    if (btnPrev) {
                        btnPrev.addEventListener('click', () => {
                            if (currentStep > 1) {
                                currentStep--;
                                showStep(currentStep);
                            }
                        });
                    }

                    if (btnNext) {
                        btnNext.addEventListener('click', () => {
                            if (currentStep < totalSteps) {
                                currentStep++;
                                showStep(currentStep);
                            }
                        });
                    }

                    // Auto open if first time
                    if (!localStorage.getItem('tatu_tutorial_seen')) {
                        setTimeout(openTutorial, 800);
                    }

                    // --- NO HOURS MODAL LOGIC ---
                    const noHoursModal = document.getElementById('no-hours-modal');
                    const closeNoHoursBtns = document.querySelectorAll('[data-close-no-hours]');

                    window.showNoHoursModal = () => {
                        if (noHoursModal) {
                            noHoursModal.classList.remove('hidden');
                            noHoursModal.classList.add('flex');
                        }
                    };

                    window.closeNoHoursModal = () => {
                        if (noHoursModal) {
                            noHoursModal.classList.add('hidden');
                            noHoursModal.classList.remove('flex');
                        }
                    };

                    closeNoHoursBtns.forEach(btn => {
                        btn.addEventListener('click', window.closeNoHoursModal);
                    });

                    // --- GENERATE CRONOGRAMA HANDLER ---
                    const generateBtn = document.querySelector('[data-generate-cronograma]');
                    if (generateBtn) {
                        generateBtn.addEventListener('click', async () => {
                            const hasHours = generateBtn.getAttribute('data-has-hours') === 'true';
                            if (!hasHours) {
                                window.showNoHoursModal();
                                return;
                            }

                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            generateBtn.setAttribute('disabled', 'disabled');
                            generateBtn.classList.add('opacity-70');

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
                                generateBtn.removeAttribute('disabled');
                                generateBtn.classList.remove('opacity-70');
                            }
                        });
                    }
                });
            </script>
</body>

</html>
