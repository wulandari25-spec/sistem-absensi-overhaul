@extends('layouts.app')

@section('title', 'Data Pegawai Outsourcing')
@section('header', 'Data Pegawai Outsourcing')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>✅ {{ session('success') }}</div>
    @endif
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <form method="GET" class="flex items-center gap-3 flex-1 max-w-lg">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, instansi..." class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <select name="institution" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                <option value="">Semua Instansi</option>
                @foreach($institutions as $inst)<option value="{{ $inst }}" {{ request('institution') == $inst ? 'selected' : '' }}>{{ $inst }}</option>@endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                <option value="">Semua Status Kehadiran</option>
                <option value="onsite" {{ request('status') == 'onsite' ? 'selected' : '' }}>Aktif di Area (Onsite)</option>
                <option value="offsite" {{ request('status') == 'offsite' ? 'selected' : '' }}>Di Luar Area (Offsite)</option>
                <option value="permit" {{ request('status') == 'permit' ? 'selected' : '' }}>Izin Hari Ini</option>
                <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sakit Hari Ini</option>
            </select>
        </form>
        @if(!auth()->user()->isK3())
        <a href="{{ route('admin.staffs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 text-white text-sm font-semibold hover:from-brand-600 hover:to-indigo-700 shadow-lg shadow-brand-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pegawai
        </a>
        @endif
    </div>
    
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Kode</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Nama</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Instansi</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Departemen</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Face Data</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                    <tr class="log-row border-b border-slate-50 dark:border-slate-800/50">
                        <td class="px-6 py-3 text-sm font-mono font-semibold text-brand-600 dark:text-brand-400">{{ $staff->staff_code }}</td>
                        <td class="px-6 py-3"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">{{ substr($staff->name, 0, 1) }}</div><span class="text-sm font-medium">{{ $staff->name }}</span></div></td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $staff->institution }}</td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $staff->department ?? '-' }}</td>
                        <td class="px-6 py-3">@if($staff->face_descriptor)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-xs font-medium">✓ Terdaftar</span>@else<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-medium">— Belum</span>@endif</td>
                        <td class="px-6 py-3">
                            @php
                                $todayAttendance = $staff->attendances()->whereDate('checked_at', today())->latest()->first();
                            @endphp
                            @if($todayAttendance && in_array($todayAttendance->status->value, ['permit', 'sick']))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-{{ $todayAttendance->status->color() }}-50 dark:bg-{{ $todayAttendance->status->color() }}-950/50 text-{{ $todayAttendance->status->color() }}-700 dark:text-{{ $todayAttendance->status->color() }}-400 text-xs font-semibold border border-{{ $todayAttendance->status->color() }}-200 dark:border-{{ $todayAttendance->status->color() }}-800">
                                    {{ $todayAttendance->status->label() }}
                                </span>
                            @elseif($staff->is_active_onsite)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Di Area</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-medium border border-slate-200 dark:border-slate-700">Di Luar</span>
                            @endif
                        </td>
                        <td class="px-6 py-3"><div class="flex items-center gap-1"><a href="{{ route('admin.staffs.show', $staff) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-950/50 transition-colors" title="Detail"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a></div></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staffs->hasPages())<div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">{{ $staffs->links() }}</div>@endif
    </div>
</div>
@endsection
