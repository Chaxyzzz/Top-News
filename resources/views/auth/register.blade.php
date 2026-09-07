@extends('layouts.app')

@section('title', 'Daftar Akun Pembaca TopNews')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#F7F7F7]">
    <div class="max-w-md w-full bg-white p-8 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-6">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <x-logo size="lg" />
            <h1 class="font-headline font-black text-xl text-[#111111] tracking-tight pt-2">
                Daftar Akun Pembaca
            </h1>
            <p class="text-xs text-[#5F6368]">
                Simpan artikel favorit Anda, ikuti diskusi beradab, dan beri respon editorial.
            </p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E] rounded-[4px] space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Register Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Nama Lengkap <span class="text-[#E50914]">*</span>
                </label>
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    autocomplete="name"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="Nama Lengkap Anda"
                />
            </div>

            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Nama Pengguna (Opsional)
                </label>
                <input 
                    id="username" 
                    name="username" 
                    type="text" 
                    value="{{ old('username') }}" 
                    autocomplete="username"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="contoh: zaki_reader"
                />
                <p class="text-[11px] text-[#80868B] mt-0.5">Biarkan kosong untuk dibuatkan otomatis.</p>
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Alamat Email <span class="text-[#E50914]">*</span>
                </label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="nama@email.com"
                />
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Kata Sandi <span class="text-[#E50914]">*</span>
                </label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="Minimal 8 karakter"
                />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Konfirmasi Kata Sandi <span class="text-[#E50914]">*</span>
                </label>
                <input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="Ulangi kata sandi Anda"
                />
            </div>

            <div class="flex items-start gap-2 pt-1">
                <input 
                    id="terms" 
                    name="terms" 
                    type="checkbox" 
                    value="1"
                    required
                    class="mt-1 w-4 h-4 rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]"
                />
                <label for="terms" class="text-xs text-[#5F6368] leading-tight">
                    Saya menyetujui <a href="{{ route('about') }}" class="text-[#E50914] underline">Pedoman Komunitas</a> dan Kebijakan Privasi TopNews.
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full py-2.5 px-4 bg-[#E50914] hover:bg-[#C8102E] text-white font-bold text-sm rounded-[4px] shadow-sm transition-colors cursor-pointer"
            >
                Daftar Sebagai Pembaca
            </button>
        </form>

        <div class="pt-4 border-t border-[#E8E8E8] text-center text-xs text-[#5F6368]">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="text-[#E50914] hover:underline font-bold ml-1">
                Masuk di sini
            </a>
        </div>
    </div>
</div>
@endsection
