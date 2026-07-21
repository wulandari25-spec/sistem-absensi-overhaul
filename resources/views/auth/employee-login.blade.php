@extends('layouts.mobile')

@section('title', 'Login Karyawan')

@section('content')
<div class="min-h-screen flex flex-col justify-between p-6 bg-slate-950 text-white">
    {{-- Header --}}
    <div class="text-center mt-12 space-y-3">
        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-brand-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-500/25">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <h1 class="text-xl font-black bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">PT PLN Nusantara Power</h1>
            <p class="text-xs text-slate-400 font-medium">Sistem Kehadiran Pegawai Outsourcing</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="my-auto w-full max-w-sm mx-auto space-y-6">
        <div class="text-center space-y-1">
            <h2 class="text-lg font-bold">Portal Presensi</h2>
            <p class="text-xs text-slate-500">Silakan masukkan kredensial pegawai Anda untuk masuk</p>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="font-semibold">⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('employee.login') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kode Staf / ID Pegawai</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        👤
                    </span>
                    <input type="text" name="staff_code" value="{{ old('staff_code') }}" placeholder="Contoh: OS-0003" class="w-full bg-slate-900 border border-slate-800 rounded-2xl pl-10 pr-4 py-3.5 text-sm text-slate-100 placeholder-slate-600 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10 transition-all font-medium" required>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        🔑
                    </span>
                    <input type="password" name="password" placeholder="Masukkan kata sandi..." class="w-full bg-slate-900 border border-slate-800 rounded-2xl pl-10 pr-4 py-3.5 text-sm text-slate-100 placeholder-slate-600 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/10 transition-all font-medium" required>
                </div>
            </div>

            <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 active:scale-[0.98] text-white font-bold text-sm tracking-wide shadow-xl shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                <span>Masuk ke Presensi →</span>
            </button>
        </form>
    </div>

    {{-- Footer --}}
    <div class="text-center pb-4 text-[10px] text-slate-600 flex flex-col gap-2">
        <p>© 2026 PT PLN Nusantara Power — Keamanan & K3 Unit Pembangkit</p>
        <div class="flex flex-col gap-2 mt-1">
            <a href="{{ route('employee.register') }}" class="text-blue-500 hover:underline font-semibold text-xs">Belum punya akun? Daftar di sini</a>
            <a href="{{ route('login') }}" class="text-slate-500 hover:underline font-semibold">Masuk sebagai Administrator</a>
        </div>
    </div>
</div>
@endsection
