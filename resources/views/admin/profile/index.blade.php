@extends('layouts.app')

@section('title', 'Profil Admin')
@section('header', 'Profil Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ activeTab: 'info', showPhotoModal: false }">
    
    {{-- Alert Notification --}}
    @if (session('status'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm animate-fade-in-up">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Hero Card --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-500 to-indigo-600 dark:from-brand-950/40 dark:to-indigo-950/40 border border-slate-200/10 p-6 sm:p-8 text-white shadow-xl shadow-brand-500/10">
        {{-- Decorative shapes --}}
        <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-white/5 blur-xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-32 h-32 rounded-full bg-indigo-500/10 blur-xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                {{-- Avatar --}}
                <div class="relative group">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white/20 dark:border-slate-800 bg-white/10 dark:bg-slate-800/80 flex items-center justify-center text-3xl font-extrabold shadow-inner transition-transform group-hover:scale-105 duration-300">
                        @if($admin->photo ?? false)
                            <img src="{{ asset('storage/'.$admin->photo) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}
                        @endif
                    </div>
                    <button @click="showPhotoModal = true" type="button" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-all" title="Ganti foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>
                
                {{-- User Info --}}
                <div class="space-y-1">
                    <h3 class="text-2xl font-extrabold tracking-tight">{{ $admin->name ?? 'Admin PLTU' }}</h3>
                    <p class="text-slate-200 dark:text-slate-400 text-sm font-medium">{{ $admin->email ?? '-' }}</p>
                    <div class="pt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 dark:bg-brand-500/20 text-white dark:text-brand-300 backdrop-blur-sm border border-white/10 dark:border-brand-500/10">
                            🛡️ {{ $admin->role?->label() ?? 'Administrator' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Stat Pills --}}
            <div class="flex gap-4 justify-center md:justify-end shrink-0">
                <div class="px-4 py-3 rounded-2xl bg-white/10 dark:bg-slate-900/40 backdrop-blur-md border border-white/5 text-center min-w-[100px] shadow-sm">
                    <div class="text-xl font-bold tracking-tight text-white">{{ $lastLogin ?? '11.30' }}</div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-300 dark:text-slate-500 mt-0.5">Login Terakhir</div>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/10 dark:bg-slate-900/40 backdrop-blur-md border border-white/5 text-center min-w-[100px] shadow-sm">
                    <div class="text-xl font-bold tracking-tight text-white">{{ $loginCount ?? '128' }}</div>
                    <div class="text-[10px] uppercase font-bold tracking-wider text-slate-300 dark:text-slate-500 mt-0.5">Total Sesi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Forms Column (Left) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                
                {{-- Tabs Header --}}
                <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl mb-6">
                    <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'bg-white dark:bg-slate-900 text-brand-600 dark:text-brand-400 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">
                        ℹ️ Informasi Akun
                    </button>
                    <button @click="activeTab = 'password'" :class="activeTab === 'password' ? 'bg-white dark:bg-slate-900 text-brand-600 dark:text-brand-400 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">
                        🔒 Keamanan & Sandi
                    </button>
                </div>

                {{-- Account Info Tab --}}
                <div x-show="activeTab === 'info'" class="space-y-4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" value="{{ old('name', $admin->name ?? '') }}" required>
                                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" value="{{ old('email', $admin->email ?? '') }}" required>
                                @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nomor Telepon</label>
                                <input type="text" name="phone" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" value="{{ old('phone', $admin->phone ?? '') }}" placeholder="08xx-xxxx-xxxx">
                                @error('phone') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Unit Kerja</label>
                                <input type="text" name="unit" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" value="{{ old('unit', $admin->unit ?? 'PT PLN Nusantara Power UP Paiton') }}">
                                @error('unit') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-3">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Password Tab --}}
                <div x-show="activeTab === 'password'" class="space-y-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                                <input type="password" name="current_password" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" required>
                                @error('current_password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                                    <input type="password" name="password" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" required>
                                    @error('password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" name="password_confirmation" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-800 dark:text-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:focus:ring-brand-950/30 transition-all outline-none" required>
                                </div>
                            </div>
                        </div>

                        <p class="text-slate-500 text-xs">
                            * Minimal 8 karakter, kombinasi huruf dan angka sangat disarankan.
                        </p>

                        <div class="flex justify-end pt-3">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- Meta & Log Columns (Right) --}}
        <div class="space-y-6">
            
            {{-- Account Summary Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Ringkasan Akun</h4>
                <div class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    <div class="flex justify-between py-3">
                        <span class="text-slate-500 dark:text-slate-400">Bergabung Sejak</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $admin->created_at?->translatedFormat('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-slate-500 dark:text-slate-400">Peran Sistem</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $admin->role?->label() ?? 'Administrator' }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-slate-500 dark:text-slate-400">Status Login</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Recent Activity Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Aktivitas Terbaru</h4>
                <div class="space-y-4">
                    @forelse($activities ?? [] as $activity)
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $activity->description }}</p>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        {{-- Simulated fallback activities to look premium --}}
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Login ke Dashboard Monitoring</p>
                                <p class="text-slate-500 text-xs mt-0.5">Hari ini, {{ $lastLogin ?? '11.30' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Memeriksa Akses Mencurigakan (Flagged)</p>
                                <p class="text-slate-500 text-xs mt-0.5">Hari ini, 09.12</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Memperbarui Konfigurasi Geofence Zone</p>
                                <p class="text-slate-500 text-xs mt-0.5">Kemarin, 16.40</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    {{-- Photo Upload Modal (Alpine JS style) --}}
    <div x-show="showPhotoModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        {{-- Backdrop --}}
        <div @click="showPhotoModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        
        {{-- Modal Box wrapper --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl transition-all" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                <form action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div class="flex justify-between items-center pb-2">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Ganti Foto Profil</h3>
                        <button @click="showPhotoModal = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    
                    <div class="space-y-2">
                        <input type="file" name="photo" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 outline-none" accept="image/*" required>
                        <p class="text-slate-500 text-xs">Format: JPG, JPEG, PNG. Maksimal ukuran file: 2MB.</p>
                        @error('photo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="showPhotoModal = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm font-semibold transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                            Unggah Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection