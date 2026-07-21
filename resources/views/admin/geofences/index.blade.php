@extends('layouts.app')

@section('title', 'Manajemen Geofence')
@section('header', 'Zona Geofence')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Pembatasan Area Kehadiran</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola zona aman GPS bagi karyawan untuk melakukan scan presensi masuk/keluar</p>
        </div>
        @if(!auth()->user()->isK3())
        <div>
            <a href="{{ route('admin.geofences.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tambah Zona Baru
            </a>
        </div>
        @endif
    </div>

    {{-- Alert Notification --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm animate-fade-in-up">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Stats Cards Group --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Zona</p>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white mt-2">{{ $zones->total() }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Area terdaftar dalam sistem</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-emerald-500/80 uppercase tracking-wider">Zona Aktif</p>
            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">{{ $zones->where('is_active', true)->count() }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Menerima verifikasi presensi</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Zona Nonaktif</p>
            <p class="text-3xl font-extrabold text-slate-500 mt-2">{{ $zones->where('is_active', false)->count() }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Mencegah verifikasi presensi</p>
        </div>
    </div>

    {{-- Geofence Zones List Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Zona</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Zona</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pusat Koordinat (GPS)</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Radius Aman</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Karyawan Onsite</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        @if(!auth()->user()->isK3())
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($zones as $zone)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-brand-600 dark:text-brand-400">
                                {{ $zone->zone_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-800 dark:text-slate-200">
                                {{ $zone->zone_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-600 dark:text-slate-400">
                                <a href="https://maps.google.com/?q={{ $zone->center_lat }},{{ $zone->center_lng }}" target="_blank" class="hover:underline hover:text-brand-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $zone->center_lat }}, {{ $zone->center_lng }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900">
                                    🌐 {{ $zone->radius_meters }} Meter
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 text-xs font-bold border border-indigo-200 dark:border-indigo-800">
                                    👥 {{ $zone->active_staff_count }} Orang
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                {{ $zone->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($zone->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            @if(!auth()->user()->isK3())
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.geofences.edit', $zone) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold transition-all">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('admin.geofences.destroy', $zone) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus zona ini?')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 font-semibold border border-red-500/10 hover:border-red-500/25 transition-all">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center"><span class="text-2xl">🗺️</span></div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Belum ada zona geofence terdaftar</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mulai batasi area presensi dengan menekan tombol Tambah Zona Baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($zones->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800">
                {{ $zones->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
