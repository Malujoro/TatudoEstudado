@props(['nome', 'assuntos' => 'n assuntos'])

<div class="bg-[#A084C3] rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-42.5 text-[#1F1F1F] shadow-sm">
    <div>
        <a href="{{ route('assuntos.index') }}" class="hover:underline transition-all">
            <h2 class="text-[28px] font-bold tracking-wide leading-tight">{{ $nome }}</h2>
        </a>
        <p class="text-sm font-medium mt-1 opacity-90">{{ $assuntos }}</p>
    </div>
    <div class="flex justify-end gap-2 mt-4">
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
