@extends('layouts.app')

@section('content')
<div class="tn-container py-8 pb-16">
    <div class="mb-6">
        <h1 class="font-headline font-bold text-2xl text-neutral-900">Ruang Pembaca</h1>
        <p class="text-xs text-neutral-500 mt-1">Kelola artikel tersimpan, jejak komentar, dan pengaturan akun Anda.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs rounded-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-300 text-rose-800 text-xs rounded-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        {{-- Reader Sidebar Nav --}}
        <aside class="md:col-span-3">
            <div class="bg-neutral-50 border border-neutral-200 rounded-sm p-4 space-y-4 shadow-xs">
                <div class="flex items-center gap-3 pb-3 border-b border-neutral-200">
                    <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm">
                        {{ auth()->user()->initials }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-sm text-neutral-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-neutral-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <nav class="space-y-1 text-xs font-semibold">
                    <a href="{{ route('account.bookmarks') }}" class="flex items-center justify-between px-3 py-2 rounded-sm transition-colors {{ request()->routeIs('account.bookmarks') ? 'bg-red-600 text-white' : 'text-neutral-700 hover:bg-neutral-200' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            Artikel Tersimpan
                        </span>
                        <span class="text-[10px] {{ request()->routeIs('account.bookmarks') ? 'text-white' : 'text-neutral-500' }}">
                            {{ auth()->user()->bookmarks()->count() }}
                        </span>
                    </a>

                    <a href="{{ route('account.comments') }}" class="flex items-center justify-between px-3 py-2 rounded-sm transition-colors {{ request()->routeIs('account.comments') ? 'bg-red-600 text-white' : 'text-neutral-700 hover:bg-neutral-200' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Komentar Saya
                        </span>
                        <span class="text-[10px] {{ request()->routeIs('account.comments') ? 'text-white' : 'text-neutral-500' }}">
                            {{ auth()->user()->comments()->count() }}
                        </span>
                    </a>

                    <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-3 py-2 rounded-sm transition-colors {{ request()->routeIs('account.profile') ? 'bg-red-600 text-white' : 'text-neutral-700 hover:bg-neutral-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Akun
                    </a>

                    <a href="{{ route('account.security') }}" class="flex items-center gap-2 px-3 py-2 rounded-sm transition-colors {{ request()->routeIs('account.security') ? 'bg-red-600 text-white' : 'text-neutral-700 hover:bg-neutral-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Keamanan & Sandi
                    </a>

                    <div class="pt-2 border-t border-neutral-200">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 rounded-sm transition-colors text-left cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </aside>

        {{-- Main Account Content --}}
        <main class="md:col-span-9">
            @yield('account_content')
        </main>
    </div>
</div>
@endsection
