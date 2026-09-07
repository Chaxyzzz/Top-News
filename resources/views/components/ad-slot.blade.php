@if ($ad)
    <div 
        class="my-6 mx-auto flex flex-col items-center justify-center p-3 bg-[#F9FAFB] border border-[#E8E8E8] rounded-[6px] text-center ad-container overflow-hidden" 
        data-ad-uuid="{{ $ad->uuid }}"
        style="max-width: {{ $ad->slot?->width ? $ad->slot->width . 'px' : '100%' }};"
    >
        {{-- Required Strict Sponsor/Advertisement Label --}}
        <div class="w-full flex items-center justify-between text-[10px] font-bold text-[#80868B] uppercase tracking-wider mb-2 px-1">
            <span>Iklan / Promosi</span>
            @if ($ad->campaign?->advertiser)
                <span class="text-[#5F6368]">Mitra: {{ $ad->campaign->advertiser }}</span>
            @endif
        </div>

        {{-- Click Tracked Destination Link (Zero Open Redirects, rel=sponsored) --}}
        <a 
            href="{{ route('ads.click', $ad->uuid) }}" 
            target="_blank" 
            rel="sponsored noopener noreferrer"
            class="block w-full group focus:outline-none"
            aria-label="Iklan: {{ $ad->name }}"
        >
            @if ($ad->media)
                <img 
                    src="{{ $ad->media->getUrl() }}" 
                    alt="{{ $ad->alt_text ?: ($ad->headline ?: $ad->name) }}" 
                    class="w-full h-auto object-contain mx-auto rounded transition-opacity group-hover:opacity-95"
                    loading="lazy"
                    width="{{ $ad->slot?->width }}"
                    height="{{ $ad->slot?->height }}"
                />
            @endif

            @if ($ad->headline || $ad->body)
                <div class="mt-2 text-left px-1">
                    @if ($ad->headline)
                        <h4 class="font-headline font-bold text-sm text-[#111111] group-hover:text-[#E50914] transition-colors leading-snug">
                            {{ $ad->headline }}
                        </h4>
                    @endif
                    @if ($ad->body)
                        <p class="text-xs text-[#5F6368] mt-0.5 line-clamp-2 leading-relaxed">
                            {{ $ad->body }}
                        </p>
                    @endif
                </div>
            @endif
        </a>
    </div>

    {{-- Impression Tracking Beacon --}}
    <script>
        (function() {
            if (window.fetch) {
                fetch('{{ route("ads.impression", $ad->uuid) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(function() {});
            }
        })();
    </script>
@endif
