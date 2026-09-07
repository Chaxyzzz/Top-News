@props([
    'items' => [],
])

<nav class="flex items-center gap-1.5 text-xs text-[#6B7280]" aria-label="Breadcrumb Admin">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#E50914] transition-colors flex items-center gap-1 font-medium">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span>Dashboard</span>
    </a>

    @foreach ($items as $item)
        <span class="text-[#D1D5DB]">/</span>
        @if (!empty($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-[#171717] transition-colors font-medium">
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-[#171717] font-semibold" aria-current="page">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
