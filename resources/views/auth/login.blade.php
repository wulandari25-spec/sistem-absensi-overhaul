@extends('layouts.mobile')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-2xl shadow-blue-500/30">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h1 class="text-2xl font-bold">Sistem Kehadiran Karyawan</h1>
            <p class="text-slate-400 text-sm mt-1">Sistem Manajemen Keamanan</p>
        </div>
        <div class="bg-slate-800/50 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-8">
            <h2 class="text-xl font-semibold mb-6">Login Admin</h2>
            @if ($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-600/50 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="admin@overhaul.id">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-600/50 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="••••••••">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-blue-500 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-slate-400">Ingat saya</label>
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all shadow-lg shadow-blue-500/25">Masuk</button>
            </form>
        </div>
        <p class="text-center text-xs text-slate-500 mt-6">&copy; {{ date('Y') }} Sistem Kehadiran Karyawan</p>
    </div>
</div>
@endsection
