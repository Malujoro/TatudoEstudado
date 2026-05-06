@props(['placeholder' => 'Pesquisar'])

<div class="relative w-full sm:w-100">
    <input type="text" placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full bg-[#BEA2E0] placeholder-[#1F1F1F]/80 text-[#1F1F1F] px-5 py-2.5 rounded-full outline-none focus:ring-2 focus:ring-[#9E82C0] font-medium text-[18px]']) }} />
    <div
        class="absolute right-3 top-1/2 -translate-y-1/2 bg-[#F0E6FA] rounded-full p-1.5 flex items-center justify-center">
        <x-icons.search class="h-5 w-5 text-[#BEA2E0]" />
    </div>
</div>
