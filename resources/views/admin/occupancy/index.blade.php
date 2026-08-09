@extends('layouts.app')

@section('title', 'Keberadaan Karyawan di Ruangan')
@section('header', 'Monitoring Ruangan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Top Action Bar & Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Monitoring Keberadaan Karyawan di Dalam Ruangan</h2>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 animate-pulse-live">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    LIVE REAL-TIME
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pantau secara langsung siapa saja karyawan yang saat ini berada di dalam ruangan / area proyek</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.occupancy.scan') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scanner Akses Ruangan (Masuk / Keluar)
            </a>
        </div>
    </div>

    {{-- Stats Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent bg-white dark:bg-slate-900 border border-emerald-500/20 dark:border-emerald-500/30 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Di Dalam Ruangan</p>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500 text-lg">🏢</span>
            </div>
            <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">{{ $totalOnsite }} <span class="text-sm font-semibold text-slate-400">Orang</span></p>
            <p class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-medium mt-1">Status: Aktif Berada di Ruangan/Area</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Di Luar Ruangan</p>
                <span class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 text-lg">🚪</span>
            </div>
            <p class="text-3xl font-black text-slate-700 dark:text-slate-300 mt-2">{{ $totalOffsite }} <span class="text-sm font-semibold text-slate-400">Orang</span></p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-1">Sudah check-out atau belum masuk</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-extrabold text-brand-500 uppercase tracking-wider">Total Karyawan</p>
                <span class="p-2 rounded-xl bg-brand-500/10 text-brand-500 text-lg">👥</span>
            </div>
            <p class="text-3xl font-black text-slate-800 dark:text-white mt-2">{{ $totalRegistered }} <span class="text-sm font-semibold text-slate-400">Orang</span></p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-1">Total karyawan registered dalam sistem</p>
        </div>
    </div>

    {{-- Zone Occupancy Breakdown Cards --}}
    <div class="space-y-3">
        <h3 class="text-sm font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kepadatan Ruangan / Zona Geofence</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($zoneStats as $stat)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-3 transition-all hover:border-brand-500/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono text-[10px] font-bold text-brand-600 dark:text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-md">{{ $stat['zone']->zone_code }}</span>
                            <h4 class="text-base font-bold text-slate-800 dark:text-white mt-1">{{ $stat['zone']->zone_name }}</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stat['onsite_count'] }}</span>
                            <span class="block text-[10px] text-slate-400 font-semibold">Orang Onsite</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span>Status Ruangan:</span>
                        @if($stat['onsite_count'] > 0)
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">🟢 Terisi ({{ $stat['onsite_count'] }} Orang)</span>
                        @else
                            <span class="font-semibold text-slate-400">⚪ Kosong</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full p-6 text-center text-xs text-slate-400">Belum ada data zona geofence terdaftar.</div>
            @endforelse
        </div>
    </div>

    {{-- Live Occupancy Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm space-y-4">
        
        {{-- Table Filter Header --}}
        <div class="p-6 pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Karyawan di Dalam Ruangan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Menampilkan karyawan yang sudah scan masuk dan belum melakukan scan keluar</p>
            </div>
            
            <form method="GET" action="{{ route('admin.occupancy.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NIP..." class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500 w-44 sm:w-56">
                
                <select name="zone_id" onchange="this.form.submit()" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                    <option value="">Semua Ruangan / Zona</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ $zoneId == $z->id ? 'selected' : '' }}>{{ $z->zone_name }}</option>
                    @endforeach
                </select>
                
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors">
                    Filter
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-y border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Instansi / Vendor</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk Ruangan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Keluar Ruangan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Durasi Ruangan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($occupancyData as $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @if($item['staff']->photo_profile)
                                        <img src="{{ asset('storage/' . $item['staff']->photo_profile) }}" alt="{{ $item['staff']->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-800 shadow-sm">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-500 to-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                            {{ strtoupper(substr($item['staff']->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="block font-bold text-slate-800 dark:text-slate-200">{{ $item['staff']->name }}</span>
                                        <span class="block text-xs font-mono font-semibold text-slate-400 dark:text-slate-500">{{ $item['staff']->staff_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-600 dark:text-slate-400">
                                {{ $item['staff']->institution }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-700 dark:text-slate-300 font-semibold">
                                🕒 {{ $item['entry_time'] ? $item['entry_time']->format('H:i:s') . ' WIB' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-700 dark:text-slate-300 font-semibold">
                                🚪 {{ $item['exit_time'] ? $item['exit_time']->format('H:i:s') . ' WIB' : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-xs text-amber-600 dark:text-amber-400">
                                ⏱️ {{ $item['duration'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($item['is_onsite'])
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Di Dalam Ruangan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-800">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        Sudah Keluar
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl">🚪</div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak Ada Karyawan di Dalam Ruangan</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Saat ini belum ada karyawan yang tercatat berada di dalam ruangan / area.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
