@props(['icon' => null, 'isPassword' => false])

<div class="relative">
    <input
        {{ $attributes->merge(['class' => 'w-full rounded-full border border-purple-night bg-white/80 px-4 py-3 text-sm text-purple-twilight placeholder:text-purple-muted focus:outline-none focus:ring-2 focus:ring-purple']) }} />

    @if ($icon && $isPassword)
        <button
            type="button"
            onclick="
                const input = this.closest('div').querySelector('input');
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                this.querySelector('.icon-show').classList.toggle('hidden', !isHidden);
                this.querySelector('.icon-hide').classList.toggle('hidden', isHidden);
            "
            class="absolute right-3 top-1/2 -translate-y-1/2 text-purple cursor-pointer">

            {{-- Ícone olho aberto (senha oculta) --}}
            <span class="icon-show">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12c2.7-4.2 5.7-6.3 9-6.3s6.3 2.1 9 6.3c-2.7 4.2-5.7 6.3-9 6.3S5.7 16.2 3 12Z"
                        stroke="currentColor" stroke-width="1.6" />
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6" />
                </svg>
            </span>

            {{-- Ícone olho fechado (senha visível) --}}
            <span class="icon-hide hidden">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3l18 18M10.5 10.677A3 3 0 0 0 13.323 13.5M7.362 7.561C5.318 8.74 3.818 10.528 3 12c2.7 4.2 5.7 6.3 9 6.3a9.15 9.15 0 0 0 3.638-.755M17 17c1.714-1.2 3-2.9 4-5-2.7-4.2-5.7-6.3-9-6.3-.729 0-1.44.1-2.13.28"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
            </span>
        </button>

    @elseif ($icon)
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-purple">
            {{ $icon }}
        </span>
    @endif
</div>