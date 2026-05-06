@props(['nome'])

<div class="bg-[#9FA089] rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-[170px] text-[#1F1F1F] shadow-sm">
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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#1F1F1F]/60 hover:text-[#1F1F1F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </button>
            <!-- Ícone de Edição -->
            <button class="hover:opacity-70 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#1F1F1F]/60 hover:text-[#1F1F1F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <!-- Ícone de Lixeira -->
            <button class="hover:opacity-70 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#1F1F1F]/60 hover:text-[#1F1F1F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</div>
