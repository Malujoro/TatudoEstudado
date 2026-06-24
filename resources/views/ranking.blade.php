@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8 pt-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-rem text-[22px] font-bold text-main-dark">Ranking de Sequências</h2>
                <p class="text-xs text-purple-muted mt-1">
                    Mantenha o foco estudando todos os dias e suba no pódio dos melhores estudantes!
                </p>
            </div>
            <div class="flex items-center gap-2 bg-purple-lightest px-4 py-2 rounded-2xl border border-purple-dim/15">
                <span class="text-lg">🔥</span>
                <span class="font-rem text-sm font-semibold text-purple-night">
                    Sua sequência: <span class="font-bold text-purple-dark">{{ auth()->user()->sequencia_estudo }}
                        dias</span>
                </span>
            </div>
        </div>

        @if ($ranking->isEmpty())
            <div class="rounded-3xl border border-purple-dim/40 bg-white/70 px-6 py-8 text-center">
                <p class="font-rem text-sm font-medium text-purple-night">
                    Nenhum estudante na sequência de estudos ainda. Comece a estudar para inaugurar o ranking!
                </p>
            </div>
        @else
            @php
                $podio = $ranking->take(3);
                $outros = $ranking->slice(3);

                // Reorganiza o podio para exibição: 2º lugar (esquerda), 1º lugar (centro), 3º lugar (direita)
                $posicoesPodio = [
                    1 => $podio->get(1) ?? null, // 2º Lugar
                    0 => $podio->get(0) ?? null, // 1º Lugar
                    2 => $podio->get(2) ?? null, // 3º Lugar
                ];

                $isUser1 = isset($posicoesPodio[0]) && $posicoesPodio[0]['id'] === auth()->id();
                $isUser2 = isset($posicoesPodio[1]) && $posicoesPodio[1]['id'] === auth()->id();
                $isUser3 = isset($posicoesPodio[2]) && $posicoesPodio[2]['id'] === auth()->id();
            @endphp

            <!-- Pódio -->
            <div class="flex items-end justify-center gap-4 md:gap-8 mt-6 max-w-xl mx-auto w-full px-4">
                <!-- 2º Lugar -->
                @if (isset($posicoesPodio[1]))
                    <div class="flex flex-col items-center flex-1 group">
                        <div class="mb-2 flex flex-col items-center justify-center text-center w-full gap-1">
                            <span class="text-2xl">🥈</span>
                            <div class="flex h-12 w-12 md:h-14 md:w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white border-2 border-slate-300 shadow-md text-slate-500 my-0.5">
                                @if ($posicoesPodio[1]['photo_url'])
                                    <img src="{{ $posicoesPodio[1]['photo_url'] }}" alt="Foto de {{ $posicoesPodio[1]['name'] }}"
                                        class="h-full w-full object-cover" />
                                @else
                                    <span class="font-rem text-base font-bold leading-none">
                                        {{ str($posicoesPodio[1]['name'] ?? 'U')->trim()->upper()->substr(0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <p
                                class="font-poppins text-xs md:text-sm font-semibold text-purple-night truncate max-w-full group-hover:text-purple-dark transition-colors">
                                {{ $posicoesPodio[1]['name'] }}
                            </p>
                            @if ($isUser2)
                                <span
                                    class="inline-block text-[9px] bg-purple-light/40 text-purple-night px-1.5 py-0.2 rounded-full font-bold">Você</span>
                            @endif
                            <p class="text-[10px] md:text-xs font-bold text-purple-muted mt-0.5">
                                {{ $posicoesPodio[1]['sequencia'] }}
                                {{ $posicoesPodio[1]['sequencia'] == 1 ? 'dia' : 'dias' }}
                            </p>
                        </div>
                        <div
                            class="w-full h-24 md:h-32 rounded-t-3xl bg-slate-200 border-2 {{ $isUser2 ? 'border-purple ring-4 ring-purple/20' : 'border-slate-300' }} shadow-md flex items-center justify-center transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <span class="font-rem text-2xl md:text-3xl font-bold text-slate-500">2º</span>
                        </div>
                    </div>
                @endif

                <!-- 1º Lugar -->
                @if (isset($posicoesPodio[0]))
                    <div class="flex flex-col items-center flex-1 group">
                        <div class="mb-2 flex flex-col items-center justify-center text-center w-full gap-1">
                            <span class="text-3xl animate-bounce inline-block">👑</span>
                            <div class="flex h-14 w-14 md:h-16 md:w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white border-2 border-amber-400 shadow-md text-amber-500 my-0.5">
                                @if ($posicoesPodio[0]['photo_url'])
                                    <img src="{{ $posicoesPodio[0]['photo_url'] }}" alt="Foto de {{ $posicoesPodio[0]['name'] }}"
                                        class="h-full w-full object-cover" />
                                @else
                                    <span class="font-rem text-lg font-bold leading-none">
                                        {{ str($posicoesPodio[0]['name'] ?? 'U')->trim()->upper()->substr(0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <p
                                class="font-poppins text-sm md:text-base font-bold text-purple-night truncate max-w-full group-hover:text-purple-deep transition-colors">
                                {{ $posicoesPodio[0]['name'] }}
                            </p>
                            @if ($isUser1)
                                <span
                                    class="inline-block text-[9px] bg-purple-light/40 text-purple-night px-1.5 py-0.2 rounded-full font-bold">Você</span>
                            @endif
                            <p class="text-xs font-extrabold text-amber-600 mt-0.5">
                                🔥 {{ $posicoesPodio[0]['sequencia'] }}
                                {{ $posicoesPodio[0]['sequencia'] == 1 ? 'dia' : 'dias' }}
                            </p>
                        </div>
                        <div
                            class="w-full h-32 md:h-40 rounded-t-3xl bg-amber-100 border-2 {{ $isUser1 ? 'border-purple ring-4 ring-purple/20' : 'border-amber-300' }} shadow-md flex items-center justify-center relative overflow-hidden transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div
                                class="absolute inset-0 bg-gradient-to-tr from-amber-200/40 via-transparent to-transparent opacity-60">
                            </div>
                            <span class="font-rem text-3xl md:text-4xl font-extrabold text-amber-500 z-10">1º</span>
                        </div>
                    </div>
                @endif

                <!-- 3º Lugar -->
                @if (isset($posicoesPodio[2]))
                    <div class="flex flex-col items-center flex-1 group">
                        <div class="mb-2 flex flex-col items-center justify-center text-center w-full gap-1">
                            <span class="text-2xl">🥉</span>
                            <div class="flex h-12 w-12 md:h-14 md:w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white border-2 border-amber-700/30 shadow-md text-amber-700/60 my-0.5">
                                @if ($posicoesPodio[2]['photo_url'])
                                    <img src="{{ $posicoesPodio[2]['photo_url'] }}" alt="Foto de {{ $posicoesPodio[2]['name'] }}"
                                        class="h-full w-full object-cover" />
                                @else
                                    <span class="font-rem text-base font-bold leading-none">
                                        {{ str($posicoesPodio[2]['name'] ?? 'U')->trim()->upper()->substr(0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <p
                                class="font-poppins text-xs md:text-sm font-semibold text-purple-night truncate max-w-full group-hover:text-purple-dark transition-colors">
                                {{ $posicoesPodio[2]['name'] }}
                            </p>
                            @if ($isUser3)
                                <span
                                    class="inline-block text-[9px] bg-purple-light/40 text-purple-night px-1.5 py-0.2 rounded-full font-bold">Você</span>
                            @endif
                            <p class="text-[10px] md:text-xs font-bold text-purple-muted mt-0.5">
                                {{ $posicoesPodio[2]['sequencia'] }}
                                {{ $posicoesPodio[2]['sequencia'] == 1 ? 'dia' : 'dias' }}
                            </p>
                        </div>
                        <div
                            class="w-full h-16 md:h-24 rounded-t-3xl bg-amber-50/80 border-2 {{ $isUser3 ? 'border-purple ring-4 ring-purple/20' : 'border-amber-700/30' }} shadow-md flex items-center justify-center transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <span class="font-rem text-2xl md:text-3xl font-bold text-amber-700/60">3º</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Demais colocações -->
            @if ($outros->isNotEmpty())
                <div class="mt-8 max-w-xl mx-auto w-full">
                    <h3 class="font-rem text-[18px] font-bold text-purple-night mb-4">Classificação geral</h3>

                    <div class="flex flex-col gap-3">
                        @foreach ($outros as $index => $item)
                            @php
                                $posicao = $index + 1;
                                $isCurrentUser = $item['id'] === auth()->id();
                            @endphp

                            <div
                                class="flex items-center justify-between rounded-2xl border px-5 py-3.5 transition-all duration-300 hover:translate-x-1 {{ $isCurrentUser ? 'border-purple bg-purple-lightest/60 shadow-sm' : 'border-purple-dim/20 bg-white hover:border-purple-dim/40' }}">
                                <div class="flex items-center gap-4">
                                    <span class="w-6 font-rem text-sm font-bold text-purple-muted text-center">
                                        {{ $posicao }}º
                                    </span>
                                    <span
                                        class="font-poppins text-sm font-semibold {{ $isCurrentUser ? 'text-purple-dark font-bold' : 'text-purple-night' }}">
                                        {{ $item['name'] }}
                                        @if ($isCurrentUser)
                                            <span
                                                class="ml-1.5 rounded-full bg-purple-light/50 px-2 py-0.5 text-[10px] font-bold text-purple-night">Você</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-sm font-bold {{ $item['sequencia'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                        {{ $item['sequencia'] }} {{ $item['sequencia'] == 1 ? 'dia' : 'dias' }}
                                    </span>
                                    @if ($item['sequencia'] > 0)
                                        <span class="text-sm">🔥</span>
                                    @else
                                        <span class="text-sm grayscale opacity-40">🔥</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection
