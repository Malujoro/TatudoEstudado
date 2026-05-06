@props(['tipo' => 'default'])

@php
    $config = match ($tipo) {
        'teoria' => ['class' => 'bg-[#B195D4] text-[#2F233B]', 'label' => 'Teoria'],
        'exercicio' => ['class' => 'bg-[#6BC5D2] text-[#114650]', 'label' => 'Exercício'],
        'revisao' => ['class' => 'bg-[#D77979] text-[#4E1D1D]', 'label' => 'Revisão'],
        default => ['class' => 'bg-[#B195D4] text-[#2F233B]', 'label' => $slot],
    };
@endphp

<span
    {{ $attributes->merge(['class' => "rounded-full px-4 py-1 font-rem text-[14px] font-medium leading-none {$config['class']}"]) }}>
    {{ $config['label'] }}
</span>
