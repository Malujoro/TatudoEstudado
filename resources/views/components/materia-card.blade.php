@props(['nome', 'assuntos' => 'n assuntos'])

<div class="bg-[#A084C3] rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-[170px] text-[#1F1F1F] shadow-sm">
    <div>
        <a href="{{ route('assuntos.index') }}" class="hover:underline transition-all">
            <h2 class="text-[28px] font-bold tracking-wide leading-tight">{{ $nome }}</h2>
        </a>
        <p class="text-sm font-medium mt-1 opacity-90">{{ $assuntos }}</p>
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <!-- Ícone de Edição -->
        <button class="hover:opacity-70 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#1F1F1F]/60 hover:text-[#1F1F1F]" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </button>
        <!-- Ícone de Lixeira -->
        <button class="hover:opacity-70 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#1F1F1F]/60 hover:text-[#1F1F1F]"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </div>
</div>
