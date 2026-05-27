@props(['tipo' => 'default'])

@php
    $label = match ($tipo) {
        'teoria' => 'Teoria',
        'exercicio' => 'Exercício',
        'revisao' => 'Revisão',
        default => $slot,
    };
@endphp

<span
    {{ $attributes->merge(['class' => "rounded-full px-4 py-2 font-rem text-[14px] font-medium leading-none tema-{$tipo}"]) }}>
    {{ $label }}
</span>
