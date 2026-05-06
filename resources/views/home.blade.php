@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center gap-4">
            <x-button>
                Gerar Cronograma
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/70 text-[#6C4A8F]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7 2v3M17 2v3M3 9h18M5 6h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </x-button>

            <div class="flex items-center gap-3">
                <x-tag class="bg-[#B195D4] text-[#2F233B]">Teoria</x-tag>
                <x-tag class="bg-[#6BC5D2] text-[#114650]">Exercício</x-tag>
                <x-tag class="bg-[#D77979] text-[#4E1D1D]">Revisão</x-tag>
            </div>
        </div>

        <div class="rounded-3xl border-2 border-dashed border-[#9C82C9] bg-[#EFEFEF] px-8 py-20 text-center">
            <p class="mx-auto max-w-3xl font-rem text-[22px] font-medium leading-snug text-[#1F1F1F]">
                Você ainda não criou um cronograma, defina seu horário disponível no perfil e clique em Gerar Cronograma
            </p>
        </div>
    </div>
@endsection
