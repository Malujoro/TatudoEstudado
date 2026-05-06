@extends('layouts.sidebar')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center gap-4">
            <x-button>
                Gerar Cronograma
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/70 text-purple-dark">
                    <x-icons.calendar class="w-4 h-4" />
                </span>
            </x-button>

            <div class="flex items-center gap-3">
                <x-tag tipo="teoria" />
                <x-tag tipo="exercicio" />
                <x-tag tipo="revisao" />
            </div>
        </div>

        <div class="rounded-3xl border-2 border-dashed border-purple bg-main-light px-8 py-20 text-center">
            <p class="mx-auto max-w-3xl font-rem text-[22px] font-medium leading-snug text-main-dark">
                Você ainda não criou um cronograma, defina seu horário disponível no perfil e clique em Gerar Cronograma
            </p>
        </div>
    </div>
@endsection
