@extends('layouts.app')

@section('title', 'Laporan Presensi')
@section('header', 'Laporan Kehadiran Karyawan')

@push('styles')
<style>
    @media print {
        .no-print,
        .no-print-important,
        .print\:hidden,
        div.no-print.print\:hidden {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showFilters: true }">
    
    {{-- Top Action Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Rekapitulasi Kehadiran</h2>
            <p class="no-print text-xs text-slate-500 dark:text-slate-400 mt-1">Pantau, filter, dan unduh laporan aktivitas presensi karyawan overhaul</p>
            
            {{-- Subtitle khusus saat dicetak --}}
            <div class="hidden print:block mt-1">
                <p class="text-sm font-bold text-slate-700">
                    @if($reportType === 'daily')
                        Rekap Kehadiran Harian (Masuk & Pulang)
                    @else
                        Log Aktivitas Absensi
                    @endif
                </p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="showFilters = !showFilters" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span x-text="showFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter'"></span>
            </button>
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="no-print inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-sm font-bold shadow-md shadow-emerald-600/15 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    {{-- Stats Cards Group --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Absensi</p>
            <p class="text-3xl font-extrabold text-slate-800 dark:text-white mt-2">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Pada rentang tanggal terpilih</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-emerald-500/80 uppercase tracking-wider">Check-In (Masuk)</p>
            <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-2">{{ $stats['check_ins'] }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Pegawai masuk area unit</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-amber-500/80 uppercase tracking-wider">Check-Out (Keluar)</p>
            <p class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-2">{{ $stats['check_outs'] }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Pegawai meninggalkan unit</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <p class="text-xs font-bold text-rose-500/80 uppercase tracking-wider">Akses Mencurigakan</p>
            <p class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-2">{{ $stats['flagged'] }}</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Telah ditandai sistem (flagged)</p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div x-show="showFilters" class="no-print bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <input type="hidden" name="report_type" value="{{ $reportType }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Filter Bulan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Bulan (Filter Cepat)</label>
                    <select name="month" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">-- Tanpa Filter Cepat --</option>
                        @foreach([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ] as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Tahun --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tahun</label>
                    <select name="year" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">-- Pilih Tahun --</option>
                        @foreach([2025, 2026, 2027] as $y)
                            <option value="{{ $y }}" {{ request('year', 2026) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Date Range --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                </div>
                
                {{-- Search --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama / Kode Pegawai</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau OS-xxxx..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-brand-500">
                </div>

                {{-- Vendor (Institution) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Perusahaan Vendor</label>
                    <select name="institution" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">Semua Vendor</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst }}" {{ request('institution') == $inst ? 'selected' : '' }}>{{ $inst }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Geofence Zone --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Zona Geofence</label>
                    <select name="geofence_zone_id" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">Semua Zona</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}" {{ request('geofence_zone_id') == $z->id ? 'selected' : '' }}>{{ $z->zone_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Status Presensi</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">Semua</option>
                        <option value="check_in" {{ request('status') == 'check_in' ? 'selected' : '' }}>Check-In (Masuk)</option>
                        <option value="check_out" {{ request('status') == 'check_out' ? 'selected' : '' }}>Check-Out (Keluar)</option>
                    </select>
                </div>

                {{-- Method --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Metode Verifikasi</label>
                    <select name="method" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">Semua</option>
                        <option value="face_recognition" {{ request('method') == 'face_recognition' ? 'selected' : '' }}>Face Recognition</option>
                        <option value="qr_code" {{ request('method') == 'qr_code' ? 'selected' : '' }}>QR Code Fallback</option>
                    </select>
                </div>

                {{-- Flagged --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Status Anomali</label>
                    <select name="is_flagged" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_flagged') === '1' ? 'selected' : '' }}>Hanya Akses Mencurigakan</option>
                        <option value="0" {{ request('is_flagged') === '0' ? 'selected' : '' }}>Hanya Akses Aman</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.reports.index', ['report_type' => $reportType]) }}" class="px-4 py-2 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 transition-all flex items-center justify-center">
                    Reset Filter
                </a>
                <button type="submit" class="px-5 py-2 bg-brand-500 hover:bg-brand-600 active:scale-95 rounded-xl text-xs font-bold text-white shadow-md shadow-brand-500/10 transition-all">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Tab Switcher --}}
    <div class="no-print print:hidden flex border-b border-slate-200 dark:border-slate-800 gap-2">
        <a href="{{ request()->fullUrlWithQuery(['report_type' => 'log']) }}" class="px-5 py-3 text-sm font-bold border-b-2 {{ $reportType === 'log' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400' }} transition-all flex items-center gap-1.5">
            <span>📝</span> Log Aktivitas Absensi
        </a>
        <a href="{{ request()->fullUrlWithQuery(['report_type' => 'daily']) }}" class="px-5 py-3 text-sm font-bold border-b-2 {{ $reportType === 'daily' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400' }} transition-all flex items-center gap-1.5">
            <span>📅</span> Rekap Kehadiran Harian (Masuk & Pulang)
        </a>
    </div>

    @if ($reportType === 'daily')
        {{-- Rekap Kehadiran Harian Table --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Durasi Kerja</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lembur</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse ($dailySummary as $row)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($row['date'])->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($row['staff']->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $row['staff']->name ?? '-' }}</span>
                                            <span class="block text-xs font-mono text-slate-400 dark:text-slate-500">{{ $row['staff']->staff_code ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                    {{ $row['staff']->institution ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $row['check_in'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-amber-600 dark:text-amber-400">
                                    {{ $row['check_out'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300 font-semibold">
                                    {{ $row['duration'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                                    @if($row['overtime'] !== '-')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 text-xs font-bold border border-blue-100 dark:border-blue-900">
                                            ⏱️ {{ $row['overtime'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-700 font-semibold">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($row['is_flagged'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900">
                                            ⚠️ Anomali
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                                            ✅ Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center"><span class="text-2xl">📭</span></div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Tidak ada data rekapitulasi harian ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($dailySummary->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800">
                    {{ $dailySummary->links() }}
                </div>
            @endif
        </div>
    @else
        {{-- Main Reports Table (Log Aktivitas) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Zona Kerja</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Koordinat</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse ($attendances as $att)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $att->checked_at->format('H:i:s') }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        {{ $att->checked_at->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($att->staff->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $att->staff->name ?? '-' }}</span>
                                            <span class="block text-xs font-mono text-slate-400 dark:text-slate-500">{{ $att->staff->staff_code ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                    {{ $att->staff->institution ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400 font-medium">
                                    {{ $att->geofenceZone->zone_name ?? 'Luar Geofence' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                    <span class="inline-flex items-center gap-1">
                                        <span>{{ $att->method->icon() }}</span>
                                        <span class="text-xs">{{ $att->method->label() }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $att->status->color() }}-50 dark:bg-{{ $att->status->color() }}-950/30 text-{{ $att->status->color() }}-700 dark:text-{{ $att->status->color() }}-400 border border-{{ $att->status->color() }}-100 dark:border-{{ $att->status->color() }}-900">
                                        {{ $att->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-500 dark:text-slate-400">
                                    @if($att->latitude && $att->longitude)
                                        <a href="https://maps.google.com/?q={{ $att->latitude }},{{ $att->longitude }}" target="_blank" class="hover:underline hover:text-brand-500 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ number_format($att->latitude, 5) }}, {{ number_format($att->longitude, 5) }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($att->is_flagged)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900" title="{{ $att->flag_reason }}">
                                            ⚠️ Mencurigakan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                                            ✅ Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center"><span class="text-2xl">📭</span></div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Tidak ada data presensi ditemukan</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Coba sesuaikan filter atau rentang tanggal Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($attendances->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Print Styles --}}
@push('styles')
<style>
    @media print {
        /* Reset tubuh halaman */
        body { background: #fff !important; color: #000 !important; font-family: Arial, sans-serif !important; }
        aside, header, button, form, nav, .top-action-bar, .no-print { display: none !important; }
        
        /* Maksimalkan lebar cetak */
        .max-w-7xl, .lg\:ml-72, .flex-1 { max-w: 100% !important; margin: 0 !important; padding: 0 !important; }
        main { padding: 0 !important; }
        
        /* Sembunyikan kartu statistik atas saat cetak agar murni tabel */
        .grid { display: none !important; }
        
        /* Hilangkan bayangan & border melengkung modern */
        .rounded-3xl, .shadow-sm, .rounded-full, .rounded-xl, .rounded-lg { border-radius: 0 !important; box-shadow: none !important; }
        
        /* Atur tabel mirip cetakan Excel */
        table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            margin-top: 15px !important; 
            background: #fff !important;
            table-layout: auto !important;
        }
        
        th, td { 
            border: 1px solid #000 !important; 
            padding: 4px 6px !important; 
            font-size: 9px !important; 
            color: #000 !important;
            background: #fff !important;
            text-align: left !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        
        th { 
            background-color: #f2f2f2 !important; 
            font-weight: bold !important;
            text-transform: uppercase !important;
        }
        
        /* Hilangkan avatar inisial bulat & icon Maps/Metode */
        table .w-8.h-8, svg, .animate-pulse { display: none !important; }
        
        /* Buat semua badge status & metode menjadi teks biasa (hilangkan latar pill hijau/merah) */
        .inline-flex, 
        span[class*="bg-"], 
        span[class*="text-"], 
        td span {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #000 !important;
            font-weight: normal !important;
            font-size: 11px !important;
        }
        
        .font-bold, .font-semibold, td span.font-bold, td span.font-semibold {
            font-weight: bold !important;
            color: #000 !important;
        }
        
        /* Sederhanakan kontainer nama & kode */
        .flex { display: block !important; }
        .items-center { align-items: stretch !important; }
        .gap-3 { gap: 0 !important; }
        a { text-decoration: none !important; color: #000 !important; }
    }
</style>
@endpush
@endsection
