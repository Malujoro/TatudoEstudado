<button
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-full bg-purple-light px-5 py-2 font-rem text-[16px] font-medium leading-none text-main-dark transition-opacity hover:opacity-80']) }}>
    {{ $slot }}
</button>
