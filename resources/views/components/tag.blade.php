@props(['tipo' => 'default'])

@php
    $config = match ($tipo) {
        'teoria' => ['class' => 'bg-purple-light text-main-dark', 'label' => 'Teoria'],
        'exercicio' => ['class' => 'bg-secondary-blue text-main-dark', 'label' => 'Exercício'],
        'revisao' => ['class' => 'bg-secondary-red text-main-dark', 'label' => 'Revisão'],
        default => ['class' => 'bg-purple-light text-main-dark', 'label' => $slot],
    };
@endphp

<span
    {{ $attributes->merge(['class' => "rounded-full px-4 py-1 font-rem text-[14px] font-medium leading-none {$config['class']}"]) }}>
    {{ $config['label'] }}
</span>
