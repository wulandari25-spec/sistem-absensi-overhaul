@extends('layouts.app')

@section('title', 'Jadwal Shift Kerja')
@section('header', 'Penjadwalan Shift Kerja Karyawan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm">
        ✅ {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Top Panel: Filters & Generator --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap items-center gap-3">
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
            <button type="submit" class="mt-5 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl transition-all">
                Terapkan
            </button>
        </form>

        @if(auth()->user()->isAdmin())
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.schedules.generate') }}">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membuat jadwal otomatis untuk bulan ini? Jadwal yang sudah ada akan dihapus.')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                    ⚡ Generate Jadwal Otomatis
                </button>
            </form>

            <form method="POST" action="{{ route('admin.schedules.clear') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh jadwal bulan ini?')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-rose-700 dark:text-rose-455 text-sm font-semibold border border-rose-200 dark:border-rose-900 transition-all">
                    🗑️ Kosongkan Jadwal
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Shift Legends --}}
    <div class="flex flex-wrap gap-4 items-center px-2">
        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Keterangan Shift:</span>
        @foreach($shifts as $s)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-{{ $s->color }}-50 dark:bg-{{ $s->color }}-950/30 text-{{ $s->color }}-700 dark:text-{{ $s->color }}-450 text-xs font-bold border border-{{ $s->color }}-100 dark:border-{{ $s->color }}-900">
                <span class="w-2 h-2 rounded-full bg-{{ $s->color }}-500"></span>
                {{ $s->name }} ({{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }})
            </span>
        @endforeach
    </div>

    {{-- Schedule Grid Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="px-4 py-3 font-bold text-slate-500 dark:text-slate-400 uppercase sticky left-0 bg-slate-50 dark:bg-slate-800 z-10 border-r border-slate-250 dark:border-slate-800" style="min-width: 150px;">Nama Karyawan</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            <th class="px-2 py-3 font-bold text-slate-500 dark:text-slate-400 text-center border-r border-slate-100 dark:border-slate-800/50" style="min-width: 35px;">
                                {{ sprintf('%02d', $d) }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($staffs as $staff)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                            {{-- Sticky Staff Name --}}
                            <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200 sticky left-0 bg-white dark:bg-slate-900 z-10 border-r border-slate-200 dark:border-slate-800 flex flex-col justify-center">
                                <span class="truncate">{{ $staff->name }}</span>
                                <span class="text-[9px] font-mono text-slate-400 dark:text-slate-500 mt-0.5">{{ $staff->staff_code }}</span>
                            </td>

                            {{-- Days Grid Cells --}}
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cellDate = \Carbon\Carbon::createFromDate($year, $month, $d);
                                    $isWithinContract = $staff->isWithinContract($cellDate);
                                    $key = $staff->id . '_' . $d;
                                    $sched = $schedules->get($key)?->first();
                                    $shift = $sched?->shift;
                                @endphp
                                <td class="p-1 text-center border-r border-slate-100 dark:border-slate-800/50 {{ !$isWithinContract ? 'bg-slate-100 dark:bg-slate-950/50 text-slate-400 dark:text-slate-600' : '' }}"
                                    @if(!$isWithinContract) title="Di luar masa kontrak kerja pegawai" @endif>
                                    @if($shift && $isWithinContract)
                                        <span 
                                            class="inline-flex w-7 h-7 items-center justify-center rounded-lg bg-{{ $shift->color }}-100 dark:bg-{{ $shift->color }}-950/40 text-{{ $shift->color }}-700 dark:text-{{ $shift->color }}-400 font-extrabold border border-{{ $shift->color }}-200 dark:border-{{ $shift->color }}-900"
                                            title="{{ $shift->name }}: {{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }}"
                                        >
                                            {{ strtoupper(substr($shift->name, 6, 1)) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-700 font-semibold">-</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 1 }}" class="px-6 py-12 text-center text-slate-500">
                                <div class="text-2xl mb-2">📭</div>
                                <p class="text-sm font-semibold">Belum ada data pegawai terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection