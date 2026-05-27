@props(['nome', 'id' => null, 'tipo' => null])


<div
    class="bg-secondary-green rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-42.5 text-main-dark shadow-sm">
    <div>
        <h2 class="text-[24px] font-bold tracking-wide leading-tight">{{ $nome }}</h2>
    </div>

    <div class="flex items-end justify-between gap-2 mt-6">
        <div class="flex flex-wrap items-center gap-2">
            @if (!empty($tipo))
                <x-tag :tipo="$tipo" />
            @else
                <x-tag tipo="teoria" />
                <x-tag tipo="exercicio" />
                <x-tag tipo="revisao" />
            @endif
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <!-- Ícone de Documento/Anotação -->
            <button class="hover:opacity-70 transition-opacity">
                <x-icons.document class="text-main-dark/60 hover:text-main-dark" />
            </button>
            <!-- Ícone de Edição -->
            <button class="hover:opacity-70 transition-opacity btn-edit-assunto">
                <x-icons.edit class="text-main-dark/60 hover:text-main-dark pointer-events-none" />
            </button>
            <!-- Ícone de Lixeira -->
            <button class="hover:opacity-70 transition-opacity btn-delete-assunto">
                <x-icons.trash class="text-main-dark/60 hover:text-main-dark pointer-events-none" />
            </button>
        </div>
    </div>
</div>
