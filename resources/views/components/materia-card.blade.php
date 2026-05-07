@props(['nome', 'id' => null, 'assuntos' => 'n assuntos'])

<div class="bg-purple rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-42.5 text-main-dark shadow-sm">
    <div>
        <a href="{{ route('assuntos.index', ['materia_id' => $id]) }}" class="hover:underline transition-all">
            <h2 class="text-[28px] font-bold tracking-wide leading-tight">{{ $nome }}</h2>
        </a>
        <p class="text-sm font-medium mt-1 opacity-90">{{ $assuntos }}</p>
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <!-- Ícone de Edição -->
        <button class="hover:opacity-70 transition-opacity btn-edit-materia">
            <x-icons.edit class="text-main-dark/60 hover:text-main-dark pointer-events-none" />
        </button>
        <!-- Ícone de Lixeira -->
        <button class="hover:opacity-70 transition-opacity btn-delete-materia">
            <x-icons.trash class="text-main-dark/60 hover:text-main-dark pointer-events-none" />
        </button>
    </div>
</div>
