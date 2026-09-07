@extends('layouts.app')

@section('title', 'Masuk ke Newsroom')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#F7F7F7]">
    <div class="max-w-md w-full bg-white p-8 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-6">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <x-logo size="lg" />
            <h1 class="font-headline font-black text-xl text-[#111111] tracking-tight pt-2">
                Akses Newsroom & Redaksi
            </h1>
            <p class="text-xs text-[#5F6368]">
                Masuk menggunakan email atau username terdaftar Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="p-3 bg-[#E6F4EA] border border-[#137333] text-xs text-[#137333] rounded-[4px]">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E] rounded-[4px] space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="login" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Email atau Username <span class="text-[#E50914]">*</span>
                </label>
                <input 
                    id="login" 
                    name="login" 
                    type="text" 
                    value="{{ old('login') }}" 
                    required 
                    autofocus
                    autocomplete="username"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="nama@topnews.id atau username"
                />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#111111]">
                        Kata Sandi <span class="text-[#E50914]">*</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs text-[#E50914] hover:text-[#C8102E] font-medium transition-colors">
                        Lupa kata sandi?
                    </a>
                </div>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    autocomplete="current-password"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="••••••••"
                />
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="remember" value="0">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]" {{ old('remember') ? 'checked' : '' }}>
                    <span class="text-[#5F6368]">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <div class="pt-2">
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Masuk ke Sistem
                </x-button>
            </div>
        </form>

        <div class="pt-4 border-t border-[#F2F2F2] text-center text-xs text-[#5F6368] space-y-1">
            <p>Belum memiliki akun pembaca? <a href="{{ route('register') }}" class="text-[#E50914] font-bold hover:underline">Daftar sekarang</a></p>
            <p class="text-[#80868B] text-[11px]">Akses administrasi & CMS khusus staf terverifikasi TopNews.</p>
        </div>
    </div>
</div>
@endsection
