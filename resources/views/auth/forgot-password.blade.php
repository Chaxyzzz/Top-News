@extends('layouts.app')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#F7F7F7]">
    <div class="max-w-md w-full bg-white p-8 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-6">
        <div class="text-center space-y-2">
            <x-logo size="lg" />
            <h1 class="font-headline font-black text-xl text-[#111111] tracking-tight pt-2">
                Pemulihan Kata Sandi
            </h1>
            <p class="text-xs text-[#5F6368] leading-relaxed">
                Masukkan alamat email yang terdaftar pada akun TopNews Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
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

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

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
                    autofocus
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    placeholder="nama@topnews.id"
                />
            </div>

            <div class="pt-2 space-y-2">
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Kirim Tautan Reset Password
                </x-button>
                <x-button variant="outline" size="md" :href="route('login')" class="w-full">
                    Kembali ke Halaman Masuk
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
