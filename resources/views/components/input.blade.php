@props(['icon' => null])

<div class="relative">
    <input
        {{ $attributes->merge(['class' => 'w-full rounded-full border border-purple-night bg-white/80 px-4 py-3 text-sm text-purple-twilight placeholder:text-purple-muted focus:outline-none focus:ring-2 focus:ring-purple']) }} />

    @if ($icon)
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-purple">
            {{ $icon }}
        </span>
    @endif
</div>
