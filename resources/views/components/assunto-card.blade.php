@props(['nome', 'id' => null, 'tipo' => null])

<div
    class="bg-secondary-green rounded-[20px] p-6 pb-4 flex flex-col justify-between min-h-42.5 text-main-dark shadow-sm group hover:bg-secondary-green/90 transition-colors h-full">
    <div>
        <h2 class="text-[24px] font-bold tracking-wide leading-tight">{{ $nome }}</h2>
    </div>

    <div class="flex items-end justify-between gap-2 mt-6">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tipos = [];
                if (is_array($tipo)) {
                    $tipos = array_values(array_filter($tipo, fn($t) => is_string($t) && $t !== ''));
                } elseif (is_string($tipo) && $tipo !== '') {
                    $tipos = [$tipo];
                }
            @endphp

            @if (!empty($tipos))
                @foreach ($tipos as $t)
                    <x-tag :tipo="$t" />
                @endforeach
            @else
                <x-tag tipo="teoria" />
                <x-tag tipo="exercicio" />
                <x-tag tipo="revisao" />
            @endif
        </div>
        <div class="flex items-center gap-1.5 shrink-0 action-buttons">
            <!-- Ícone de Documento/Anotação -->
            <button class="hover:opacity-70 transition-opacity btn-caderno-assunto">
                <x-icons.document class="text-main-dark/60 hover:text-main-dark pointer-events-none" />
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
