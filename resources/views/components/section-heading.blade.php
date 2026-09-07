@props([
    'title',
    'subtitle' => null,
    'link' => null,
    'linkText' => 'Lihat Semua',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between pb-3 mb-6 border-b border-[#E8E8E8]']) }}>
    <div>
        <h2 class="tn-section-title">
            {{ $title }}
        </h2>
        @if ($subtitle)
            <p class="mt-0.5 text-xs text-[#5F6368]">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($link)
        <a href="{{ $link }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#E50914] hover:text-[#C8102E] tracking-tight group transition-colors">
            <span>{{ $linkText }}</span>
            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @endif
</div>
