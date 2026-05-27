@props(['placeholder' => 'Pesquisar', 'containerClass' => 'w-full sm:w-100'])

<div class="relative {{ $containerClass }}">
    <input type="text" placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full bg-purple-light placeholder-main-dark/80 text-main-dark px-5 py-2.5 rounded-full outline-none focus:ring-2 focus:ring-purple font-medium text-[18px]']) }} />
    <div
        class="absolute right-3 top-1/2 -translate-y-1/2 bg-purple-lightest rounded-full p-1.5 flex items-center justify-center">
        <x-icons.search class="h-5 w-5 text-purple-light" />
    </div>
</div>
