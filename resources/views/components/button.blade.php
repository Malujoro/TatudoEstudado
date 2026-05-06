<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'group inline-flex items-center justify-between gap-4 rounded-full bg-[#B195D4] px-6 py-3 text-sm font-semibold text-[#2F233B] shadow-[0_12px_30px_rgba(98,72,132,0.25)] hover:bg-[#A486CF] transition']) }}
>
    <span>{{ $slot }}</span>
    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/70 text-[#6C4A8F] group-hover:bg-white">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5 12h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
</button>