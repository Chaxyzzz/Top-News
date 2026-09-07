@props([
    'type' => 'info', // 'success', 'error', 'warning', 'info'
    'message',
    'timeout' => 4000,
])

@php
    $typeStyles = [
        'success' => 'bg-[#111111] text-white border-l-4 border-[#0F9D58]',
        'error' => 'bg-[#111111] text-white border-l-4 border-[#E50914]',
        'warning' => 'bg-[#111111] text-white border-l-4 border-[#F4B400]',
        'info' => 'bg-[#111111] text-white border-l-4 border-[#4285F4]',
    ];
    $style = $typeStyles[$type] ?? $typeStyles['info'];
@endphp

<div class="tn-toast fixed bottom-5 right-5 z-50 flex items-center justify-between gap-4 px-4 py-3 rounded-[4px] shadow-dropdown text-sm transition-all duration-300 {{ $style }}" data-timeout="{{ $timeout }}" role="alert">
    <span>{{ $message }}</span>
    <button type="button" class="tn-toast-dismiss text-gray-400 hover:text-white transition-colors" aria-label="Tutup">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
