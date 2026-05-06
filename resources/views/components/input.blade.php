@props(['icon' => null])

<div class="relative">
    <input {{ $attributes->merge(['class' => 'w-full rounded-full border border-[#3E2F4D] bg-white/80 px-4 py-3 text-sm text-[#4C3D5F] placeholder:text-[#8B7AA0] focus:outline-none focus:ring-2 focus:ring-[#A78AC9]']) }} />
    
    @if($icon)
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#A78AC9]">
            {{ $icon }}
        </span>
    @endif
</div>