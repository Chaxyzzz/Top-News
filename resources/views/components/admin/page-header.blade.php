@props([
    'title',
    'description' => null,
])

<div class="space-y-2 border-b border-[#E5E7EB] pb-5 mb-6">
    @if (isset($breadcrumbs))
        <div class="mb-2">
            {{ $breadcrumbs }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl sm:text-3xl text-[#171717] tracking-tight">
                {{ $title }}
            </h1>
            @if ($description)
                <p class="text-xs text-[#6B7280] mt-1 max-w-2xl leading-relaxed">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if (isset($actions))
            <div class="flex items-center gap-2.5 flex-wrap">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
