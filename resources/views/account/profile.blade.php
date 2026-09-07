@extends('account.layout')

@section('title', 'Profil Akun — Ruang Pembaca TopNews')

@section('account_content')
<div class="bg-white border border-neutral-200 rounded-sm p-6 shadow-xs">
    <div class="pb-4 mb-6 border-b border-neutral-200">
        <h2 class="font-headline font-bold text-lg text-neutral-900">Profil Pembaca</h2>
        <p class="text-xs text-neutral-500 mt-0.5">Informasi identitas akun publik Anda di TopNews.</p>
    </div>

    <form action="{{ route('account.profile.update') }}" method="POST" class="max-w-md space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1">
                Nama Lengkap <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', $user->name) }}" 
                required
                class="w-full text-sm border border-neutral-300 rounded-sm px-3.5 py-2 focus:outline-none focus:border-red-600"
            />
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="username" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1">
                Nama Pengguna <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                value="{{ old('username', $user->username) }}" 
                required
                class="w-full text-sm border border-neutral-300 rounded-sm px-3.5 py-2 focus:outline-none focus:border-red-600"
            />
            @error('username')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1">
                Alamat Email
            </label>
            <input 
                type="email" 
                id="email" 
                value="{{ $user->email }}" 
                disabled
                class="w-full text-sm border border-neutral-200 bg-neutral-100 text-neutral-500 rounded-sm px-3.5 py-2 cursor-not-allowed"
            />
            <p class="text-[11px] text-neutral-400 mt-1">Alamat email terikat pada akun dan tidak dapat diubah secara mandiri.</p>
        </div>

        <div class="pt-2">
            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-sm transition-colors cursor-pointer shadow-xs">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
