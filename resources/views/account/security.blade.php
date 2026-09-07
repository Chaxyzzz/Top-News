@extends('account.layout')

@section('title', 'Keamanan Akun — Ruang Pembaca TopNews')

@section('account_content')
<div class="bg-white border border-neutral-200 rounded-sm p-6 shadow-xs">
    <div class="pb-4 mb-6 border-b border-neutral-200">
        <h2 class="font-headline font-bold text-lg text-neutral-900">Keamanan & Kata Sandi</h2>
        <p class="text-xs text-neutral-500 mt-0.5">Perbarui kata sandi akun untuk menjaga keamanan sesi baca Anda.</p>
    </div>

    <form action="{{ route('account.security.update') }}" method="POST" class="max-w-md space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1">
                Kata Sandi Saat Ini <span class="text-red-600">*</span>
            </label>
            <input 
                type="password" 
                id="current_password" 
                name="current_password" 
                required
                autocomplete="current-password"
                class="w-full text-sm border border-neutral-300 rounded-sm px-3.5 py-2 focus:outline-none focus:border-red-600"
            />
            @error('current_password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1">
                Kata Sandi Baru <span class="text-red-600">*</span>
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full text-sm border border-neutral-300 rounded-sm px-3.5 py-2 focus:outline-none focus:border-red-600"
            />
            @error('password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1">
                Konfirmasi Kata Sandi Baru <span class="text-red-600">*</span>
            </label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required
                autocomplete="new-password"
                class="w-full text-sm border border-neutral-300 rounded-sm px-3.5 py-2 focus:outline-none focus:border-red-600"
            />
        </div>

        <div class="pt-2">
            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-sm transition-colors cursor-pointer shadow-xs">
                Perbarui Kata Sandi
            </button>
        </div>
    </form>
</div>
@endsection
