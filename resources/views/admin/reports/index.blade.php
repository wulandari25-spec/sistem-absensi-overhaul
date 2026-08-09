@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi Presensi')
@section('header', 'Rekapitulasi Kehadiran Bulanan')

@push('styles')
<style>
    @page {
        size: landscape;
        margin: 5mm 6mm 5mm 6mm;
    }
    @media print {
        body { 
            background: #ffffff !important; 
            color: #000000 !important; 
            font-family: Arial, Helvetica, sans-serif !important; 
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .no-print, aside, header, nav, form, .top-action-bar { 
            display: none !important; 
        }
        .print-only { 
            display: block !important; 
        }
        .max-w-7xl, .w-full { 
            max-width: 100% !important; 
            width: 100% !important; 
            margin: 0 !important; 
            padding: 0 !important; 
        }
        [class*="lg:ml-"] { 
            margin-left: 0 !important; 
        }
        main { 
            padding: 0 !important; 
            margin: 0 !important; 
        }
        .rounded-3xl, .shadow-sm, .rounded-2xl { 
            border-radius: 0 !important; 
            box-shadow: none !important; 
            border: none !important; 
        }
        .overflow-x-auto {
            overflow: visible !important;
        }
        table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            table-layout: fixed !important; 
            font-size: 6.5pt !important;
        }
        th, td { 
            border: 1px solid #334155 !important; 
            padding: 1.5px 0.5px !important; 
            text-align: center !important; 
            color: #000000 !important; 
            font-size: 6.5pt !important;
            word-break: break-all;
        }
        th { 
            background-color: #fed7aa !important; /* Amber-200 */
            font-weight: bold !important; 
            color: #000 !important;
            padding: 2px 0.5px !important;
        }
        .th-sub {
            background-color: #ffedd5 !important; /* Amber-100 */
        }
        .weekend-cell { 
            background-color: #f1f5f9 !important; 
            color: #b91c1c !important; 
        }
        td.staff-col { 
            text-align: left !important; 
            padding-left: 3px !important; 
            overflow: hidden !important;
        }
        td.staff-col span {
            display: block !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .signature-section {
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Kop / Header Cetak Landscape (Hanya tampil saat Print) --}}
    <div class="hidden print-only mb-3 border-b-2 border-slate-900 pb-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-base">
                    ⚡
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-tight uppercase text-slate-900 leading-tight">PT PLN (PERSERO)</h1>
                    <h2 class="text-[11px] font-bold text-slate-700 tracking-wide">REKAPITULASI KEHADIRAN KARYAWAN OVERHAUL</h2>
                </div>
            </div>
            <div class="text-right text-[9px] text-slate-600 space-y-0.5 font-medium">
                <p><span class="font-bold">Bulan / Periode:</span> {{ $stats['month_name'] }}</p>
                <p><span class="font-bold">Total Karyawan:</span> {{ count($allStaffs) }} Orang</p>
                <p><span class="font-bold">Tanggal Cetak:</span> {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>

    {{-- Alert Notification --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm no-print">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    {{-- Top Filter & Action Bar --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6 no-print">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bulan</label>
                <select name="month" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                    @foreach([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ] as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun</label>
                <select name="year" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                    @foreach([2025, 2026, 2027] as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Vendor (Instansi)</label>
                <select name="institution" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                    <option value="">Semua Vendor</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst }}" {{ request('institution') === $inst ? 'selected' : '' }}>{{ $inst }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Nama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Kode..." class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl transition-all">
                Terapkan
            </button>
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-sm font-bold shadow-md shadow-emerald-600/15 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Rekap (Landscape)
            </button>
        </div>
    </div>

    {{-- Keterangan Legenda Status --}}
    <div class="flex flex-wrap gap-4 items-center px-2 no-print">
        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Keterangan:</span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold border border-emerald-100 dark:border-emerald-900">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            ✓ = Hadir
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-xs font-bold border border-amber-100 dark:border-amber-900">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            I = Izin
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 text-xs font-bold border border-rose-100 dark:border-rose-900">
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            S = Sakit
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold border border-slate-200 dark:border-slate-700">
            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            - = Alpa / Libur
        </span>
    </div>

    {{-- 1. TABEL TAMPILAN LAYAR (DENGAN PAGINATION) --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm no-print">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs">
                <colgroup>
                    <col style="width: 2.5%;">
                    <col style="width: 15.5%;">
                    @foreach ($daysList as $d)
                        <col style="width: {{ 72.0 / $daysInMonth }}%;">
                    @endforeach
                    <col style="width: 2.5%;">
                    <col style="width: 2.5%;">
                    <col style="width: 2.5%;">
                    <col style="width: 2.5%;">
                </colgroup>
                <thead>
                    {{-- Row 1 Header: Header Bulan & Rentang Tanggal --}}
                    <tr class="bg-amber-100 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-900/60 text-slate-800 dark:text-slate-200 font-bold">
                        <th rowspan="2" class="px-1 py-1.5 text-center border-r border-amber-200 dark:border-amber-900/60 sticky left-0 bg-amber-100 dark:bg-slate-900 z-10">No</th>
                        <th rowspan="2" class="px-2 py-1.5 border-r border-amber-200 dark:border-amber-900/60 sticky left-8 bg-amber-100 dark:bg-slate-900 z-10 staff-col">
                            {{ $stats['month_name'] }}
                        </th>
                        <th colspan="{{ $daysInMonth }}" class="px-1 py-1 text-center border-r border-amber-200 dark:border-amber-900/60 uppercase tracking-wider text-[11px]">
                            Tanggal
                        </th>
                        <th colspan="4" class="px-1 py-1 text-center text-slate-800 dark:text-slate-200 font-bold border-l border-amber-200 dark:border-amber-900/60 uppercase tracking-wider text-[9px]">
                            Total
                        </th>
                    </tr>

                    {{-- Row 2 Header: Angka Tanggal & Nama Hari & Sub-kolom Total --}}
                    <tr class="bg-amber-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 font-semibold">
                        @foreach ($daysList as $d)
                            <th class="p-0.5 text-center border-r border-slate-200 dark:border-slate-800 th-sub {{ $d['is_weekend'] ? 'weekend-cell bg-amber-100/60 dark:bg-amber-950/30 text-rose-600 dark:text-rose-400' : '' }}">
                                <div class="text-[9.5px] font-bold leading-none">{{ $d['day'] }}</div>
                                <div class="text-[7.5px] uppercase font-mono leading-none mt-0.5">{{ $d['day_name'] }}</div>
                            </th>
                        @endforeach
                        {{-- Sub-kolom Summary --}}
                        <th class="p-0.5 text-center font-bold text-emerald-700 dark:text-emerald-400 border-r border-slate-200 dark:border-slate-800 th-sub" title="Total Hadir">H</th>
                        <th class="p-0.5 text-center font-bold text-amber-700 dark:text-amber-400 border-r border-slate-200 dark:border-slate-800 th-sub" title="Total Izin">I</th>
                        <th class="p-0.5 text-center font-bold text-rose-700 dark:text-rose-400 border-r border-slate-200 dark:border-slate-800 th-sub" title="Total Sakit">S</th>
                        <th class="p-0.5 text-center font-bold text-slate-700 dark:text-slate-300 th-sub" title="Total Alpa">A</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse ($staffs as $staff)
                        @php
                            $totalHadir = 0;
                            $totalIzin = 0;
                            $totalSakit = 0;
                            $totalAlpa = 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                            {{-- No --}}
                            <td class="px-1 py-1.5 text-center text-slate-500 dark:text-slate-400 font-semibold border-r border-slate-200 dark:border-slate-800 sticky left-0 bg-white dark:bg-slate-900 z-10">
                                {{ $loop->iteration + ($staffs->currentPage() - 1) * $staffs->perPage() }}
                            </td>

                            {{-- Nama & Kode Staf --}}
                            <td class="px-2 py-1.5 font-bold text-slate-800 dark:text-slate-200 border-r border-slate-200 dark:border-slate-800 sticky left-8 bg-white dark:bg-slate-900 z-10 staff-col flex flex-col justify-center overflow-hidden">
                                <span class="truncate">{{ $staff->name }}</span>
                                <span class="text-[8.5px] font-mono text-slate-400 dark:text-slate-500 font-normal leading-none truncate">{{ $staff->staff_code }} • {{ $staff->institution ?? '-' }}</span>
                            </td>

                            {{-- Tanggal Cells --}}
                            @foreach ($daysList as $d)
                                @php
                                    $cellDate = $d['date'];
                                    $isWithinContract = $staff->isWithinContract($cellDate);
                                    $key = $staff->id . '_' . $d['day'];
                                    $dayLogs = $attendances->get($key, collect());
                                    
                                    $isHadir = $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_IN) || $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_OUT);
                                    $isIzin = $dayLogs->contains('status', \App\Enums\AttendanceStatus::PERMIT);
                                    $isSakit = $dayLogs->contains('status', \App\Enums\AttendanceStatus::SICK);
                                    
                                    if ($isHadir) {
                                        $totalHadir++;
                                    } elseif ($isIzin) {
                                        $totalIzin++;
                                    } elseif ($isSakit) {
                                        $totalSakit++;
                                    } elseif ($isWithinContract && !$d['is_weekend']) {
                                        $totalAlpa++;
                                    }
                                @endphp
                                <td class="p-0.5 text-center border-r border-slate-100 dark:border-slate-800/50 {{ $d['is_weekend'] ? 'weekend-cell bg-slate-50/50 dark:bg-slate-950/20' : '' }} {{ (!$isHadir && !$isIzin && !$isSakit && !$isWithinContract) ? 'bg-slate-100/50 dark:bg-slate-950/40 text-slate-300 dark:text-slate-700' : '' }}">
                                    @if ($isHadir)
                                        <span class="inline-flex w-4 h-4 items-center justify-center rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-black text-[10px]" title="Hadir">
                                            ✓
                                        </span>
                                    @elseif ($isIzin)
                                        <span class="inline-flex w-4 h-4 items-center justify-center rounded bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-bold text-[8.5px]" title="Izin">
                                            I
                                        </span>
                                    @elseif ($isSakit)
                                        <span class="inline-flex w-4 h-4 items-center justify-center rounded bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-[8.5px]" title="Sakit">
                                            S
                                        </span>
                                    @elseif (!$isWithinContract)
                                        <span class="text-slate-300 dark:text-slate-700" title="Di luar masa kontrak">-</span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-700">-</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Summary Numbers --}}
                            <td class="p-0.5 text-center font-bold text-emerald-700 dark:text-emerald-400 border-r border-slate-100 dark:border-slate-800 text-[10px]">
                                {{ $totalHadir }}
                            </td>
                            <td class="p-0.5 text-center font-bold text-amber-700 dark:text-amber-400 border-r border-slate-100 dark:border-slate-800 text-[10px]">
                                {{ $totalIzin }}
                            </td>
                            <td class="p-0.5 text-center font-bold text-rose-700 dark:text-rose-400 border-r border-slate-100 dark:border-slate-800 text-[10px]">
                                {{ $totalSakit }}
                            </td>
                            <td class="p-0.5 text-center font-bold text-slate-600 dark:text-slate-400 text-[10px]">
                                {{ $totalAlpa }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 6 }}" class="px-6 py-12 text-center text-slate-500">
                                <div class="text-2xl mb-2">📭</div>
                                <p class="text-sm font-semibold">Tidak ada data karyawan ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if ($staffs->hasPages())
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800 no-print">
                {{ $staffs->links() }}
            </div>
        @endif
    </div>

    {{-- 2. TABEL KHUSUS CETAK PRINT (MEMUAT SELURUH DATA KARYAWAN TANPA TERPOTONG PAGINASI) --}}
    <div class="hidden print-only">
        <table class="w-full border-collapse text-left">
            <colgroup>
                <col style="width: 2.5%;">
                <col style="width: 15.5%;">
                @foreach ($daysList as $d)
                    <col style="width: {{ 72.0 / $daysInMonth }}%;">
                @endforeach
                <col style="width: 2.5%;">
                <col style="width: 2.5%;">
                <col style="width: 2.5%;">
                <col style="width: 2.5%;">
            </colgroup>
            <thead>
                <tr class="bg-amber-100 font-bold">
                    <th rowspan="2">No</th>
                    <th rowspan="2" class="staff-col">{{ $stats['month_name'] }}</th>
                    <th colspan="{{ $daysInMonth }}" class="uppercase tracking-wider">Tanggal</th>
                    <th colspan="4" class="uppercase tracking-wider">Total</th>
                </tr>
                <tr class="bg-amber-50 font-semibold">
                    @foreach ($daysList as $d)
                        <th class="th-sub {{ $d['is_weekend'] ? 'weekend-cell' : '' }}">
                            <div>{{ $d['day'] }}</div>
                            <div style="font-size: 6pt;">{{ $d['day_name'] }}</div>
                        </th>
                    @endforeach
                    <th class="th-sub">H</th>
                    <th class="th-sub">I</th>
                    <th class="th-sub">S</th>
                    <th class="th-sub">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allStaffs as $staff)
                    @php
                        $totalHadir = 0;
                        $totalIzin = 0;
                        $totalSakit = 0;
                        $totalAlpa = 0;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="staff-col">
                            <span>{{ $staff->name }}</span>
                            <span style="font-size: 5.5pt; color: #555;">{{ $staff->staff_code }} • {{ $staff->institution ?? '-' }}</span>
                        </td>
                        @foreach ($daysList as $d)
                            @php
                                $cellDate = $d['date'];
                                $isWithinContract = $staff->isWithinContract($cellDate);
                                $key = $staff->id . '_' . $d['day'];
                                $dayLogs = $attendances->get($key, collect());
                                
                                $isHadir = $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_IN) || $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_OUT);
                                $isIzin = $dayLogs->contains('status', \App\Enums\AttendanceStatus::PERMIT);
                                $isSakit = $dayLogs->contains('status', \App\Enums\AttendanceStatus::SICK);
                                
                                if ($isHadir) {
                                    $totalHadir++;
                                } elseif ($isIzin) {
                                    $totalIzin++;
                                } elseif ($isSakit) {
                                    $totalSakit++;
                                } elseif ($isWithinContract && !$d['is_weekend']) {
                                    $totalAlpa++;
                                }
                            @endphp
                            <td class="{{ $d['is_weekend'] ? 'weekend-cell' : '' }}">
                                @if ($isHadir)
                                    <b>✓</b>
                                @elseif ($isIzin)
                                    <b>I</b>
                                @elseif ($isSakit)
                                    <b>S</b>
                                @elseif (!$isWithinContract)
                                    -
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                        <td><b>{{ $totalHadir }}</b></td>
                        <td>{{ $totalIzin }}</td>
                        <td>{{ $totalSakit }}</td>
                        <td>{{ $totalAlpa }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tanda Tangan Mengetahui (Hanya tampil saat Print) --}}
    <div class="hidden print-only signature-section mt-6 pt-3">
        <div class="grid grid-cols-3 text-center text-[9px] text-slate-900 font-medium">
            <div>
                <p>Dibuat Oleh,</p>
                <p class="font-bold uppercase mt-0.5">Petugas Pengawas Absensi</p>
                <div class="h-12"></div>
                <p class="font-bold underline">( {{ auth()->user()->name ?? 'Administrator' }} )</p>
                <p class="text-[8px] text-slate-500">Security & Administration</p>
            </div>
            <div>
                <p>Diperiksa Oleh,</p>
                <p class="font-bold uppercase mt-0.5">Team Leader K3 & Kam</p>
                <div class="h-12"></div>
                <p class="font-bold underline">( ............................................ )</p>
                <p class="text-[8px] text-slate-500">K3 & Lingkungan</p>
            </div>
            <div>
                <p>Disetujui Oleh,</p>
                <p class="font-bold uppercase mt-0.5">Manager Bagian Overhaul</p>
                <div class="h-12"></div>
                <p class="font-bold underline">( ............................................ )</p>
                <p class="text-[8px] text-slate-500">Leader Management Overhaul</p>
            </div>
        </div>
    </div>

</div>
@endsection
