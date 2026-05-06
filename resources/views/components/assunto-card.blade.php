@props(['nome'])

<div class="bg-[#9FA089] rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-42.5 text-[#1F1F1F] shadow-sm">
    <div>
        <h2 class="text-[24px] font-bold tracking-wide leading-tight">{{ $nome }}</h2>
    </div>

    <div class="flex items-end justify-between gap-2 mt-6">
        <div class="flex flex-wrap items-center gap-2">
            <x-tag class="bg-[#B195D4] text-[#2F233B]">Teoria</x-tag>
            <x-tag class="bg-[#6BC5D2] text-[#114650]">Exercício</x-tag>
            <x-tag class="bg-[#D77979] text-[#4E1D1D]">Revisão</x-tag>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <!-- Ícone de Documento/Anotação -->
            <button class="hover:opacity-70 transition-opacity">
                <x-icons.document class="text-[#1F1F1F]/60 hover:text-[#1F1F1F]" />
            </button>
            <!-- Ícone de Edição -->
            <button class="hover:opacity-70 transition-opacity">
                <x-icons.edit class="text-[#1F1F1F]/60 hover:text-[#1F1F1F]" />
            </button>
            <!-- Ícone de Lixeira -->
            <button class="hover:opacity-70 transition-opacity">
                <x-icons.trash class="text-[#1F1F1F]/60 hover:text-[#1F1F1F]" />
            </button>
        </div>
    </div>
</div>
