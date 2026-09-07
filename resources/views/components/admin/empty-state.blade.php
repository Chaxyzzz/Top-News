@props([
    'title' => 'Tidak Ada Data',
    'description' => 'Belum ada rekaman data yang dapat ditampilkan saat ini.',
    'actionLabel' => null,
    'actionUrl' => null,
])

<div class="py-12 px-4 text-center">
    <div class="w-12 h-12 rounded-full bg-[#F3F4F6] text-[#9CA3AF] flex items-center justify-center mx-auto mb-3">
        @if (isset($icon))
            {{ $icon }}
        @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>

    <h4 class="font-headline font-bold text-base text-[#171717] mb-1">
        {{ $title }}
    </h4>
    <p class="text-xs text-[#6B7280] max-w-sm mx-auto mb-4 leading-relaxed">
        {{ $description }}
    </p>

    @if ($actionLabel && $actionUrl)
        <x-button variant="primary" size="sm" :href="$actionUrl">
            {{ $actionLabel }}
        </x-button>
    @endif
</div>
