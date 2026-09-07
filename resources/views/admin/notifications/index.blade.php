@extends('layouts.admin')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Pusat Notifikasi Redaksi" 
        description="Pantau interaksi pembaca, pengiriman pesan kontak, dan komentar publik terkini."
    >
        <x-slot name="actions">
            @if($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <x-button type="submit" variant="outline" size="sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Tandai Semua Dibaca</span>
                    </x-button>
                </form>
            @endif
        </x-slot>
    </x-admin.page-header>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-[#E8E8E8] pb-3">
        <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" 
           class="px-4 py-2 text-xs font-bold rounded-[4px] transition-colors {{ $filter === 'all' ? 'bg-[#111111] text-white' : 'bg-[#F4F4F4] text-[#555555] hover:bg-[#E8E8E8]' }}">
            Semua ({{ Auth::user()->notifications()->count() }})
        </a>
        <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" 
           class="px-4 py-2 text-xs font-bold rounded-[4px] transition-colors {{ $filter === 'unread' ? 'bg-[#E50914] text-white' : 'bg-[#F4F4F4] text-[#555555] hover:bg-[#E8E8E8]' }}">
            Belum Dibaca ({{ $unreadCount }})
        </a>
    </div>

    <!-- Notification List -->
    <div class="bg-white border border-[#E8E8E8] rounded-[6px] shadow-sm divide-y divide-[#E8E8E8]">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = is_null($notification->read_at);
            @endphp
            <div class="p-4 sm:p-5 flex items-start gap-4 transition-colors {{ $isUnread ? 'bg-[#FFF9F9]' : 'hover:bg-[#F9F9F9]' }}">
                <!-- Icon -->
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $isUnread ? 'bg-[#E50914]/10 text-[#E50914]' : 'bg-[#F0F0F0] text-[#777777]' }}">
                    @if(($data['icon'] ?? '') === 'mail')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    @elseif(($data['icon'] ?? '') === 'chat')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h4 class="text-sm font-bold text-[#111111] flex items-center gap-2">
                            {{ $data['title'] ?? 'Notifikasi' }}
                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full bg-[#E50914] inline-block"></span>
                            @endif
                        </h4>
                        <span class="text-[11px] text-[#777777] shrink-0 font-medium">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-[#444444] leading-relaxed mb-3">
                        {{ $data['message'] ?? '' }}
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.notifications.read', $notification->id) }}" 
                           class="inline-flex items-center gap-1 text-xs font-bold text-[#E50914] hover:underline">
                            <span>Buka Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-[#CCCCCC] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="text-sm font-bold text-[#222222] mb-1">Belum ada notifikasi</h3>
                <p class="text-xs text-[#777777]">Aktivitas pembaca dan pesan masuk akan muncul di sini secara otomatis.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
