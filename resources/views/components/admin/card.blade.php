@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[8px] border border-[#E5E7EB] shadow-subtle overflow-hidden']) }}>
    @if ($title || isset($headerActions))
        <div class="p-5 border-b border-[#F3F4F6] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                @if ($title)
                    <h3 class="font-headline font-bold text-base text-[#171717]">
                        {{ $title }}
                    </h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            @if (isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-5">
        {{ $slot }}
    </div>
</div>
