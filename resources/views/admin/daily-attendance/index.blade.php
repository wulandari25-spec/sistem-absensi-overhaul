@extends('layouts.app')

@section('title', 'Absensi Harian')
@section('header', 'Absensi Harian Karyawan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    showManualModal: false,
    selectedStaffId: '',
    statusType: 'check_in',
    openManualWithStaff(staffId) {
        this.selectedStaffId = staffId;
        this.statusType = 'check_in';
        this.showManualModal = true;
    }
}">
    {{-- Alert Notification --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-sm animate-fade-in-up">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Top Action & Filter Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Pemantauan & Presensi Harian</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kelola absensi manual dan periksa kehadiran realtime karyawan hari ini</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(!auth()->user()->isK3())
            <a href="{{ route('admin.daily-attendance.scan') }}" target="_blank" class="inline-flex items-center gap-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-sm font-bold shadow-md shadow-indigo-600/15 transition-all h-[42px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Scan Wajah (Kamera)
            </a>
            <button @click="selectedStaffId = ''; statusType = 'check_in'; showManualModal = true" class="inline-flex items-center gap-2 px-4 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-sm font-bold shadow-md shadow-brand-500/10 transition-all h-[42px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Presensi Manual
            </button>
            @endif
        </div>
    </div>

    {{-- Stats Cards Group --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Karyawan</p>
            <p class="text-2xl font-black text-slate-850 dark:text-white mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-emerald-500/80 uppercase tracking-wider">Hadir</p>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['present'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-amber-500/80 uppercase tracking-wider">Izin</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $stats['permit'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <p class="text-[10px] font-bold text-red-500/80 uppercase tracking-wider">Sakit</p>
            <p class="text-2xl font-black text-red-650 dark:text-red-400 mt-1">{{ $stats['sick'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm col-span-2 lg:col-span-1">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alpa (Belum Hadir)</p>
            <p class="text-2xl font-black text-slate-500 mt-1">{{ $stats['absent'] }}</p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.daily-attendance.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ $dateStr }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cari Pegawai</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode pegawai..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Vendor</label>
                <div class="flex gap-2">
                    <select name="institution" class="flex-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">Semua Vendor</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst }}" {{ request('institution') == $inst ? 'selected' : '' }}>{{ $inst }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-bold shadow-md shadow-brand-500/10 transition-all">
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Attendance Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Info Tambahan</th>
                        @if(!auth()->user()->isK3())
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse ($dailyData as $data)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @if($data['staff']->photo_profile)
                                        <img src="{{ asset('storage/' . $data['staff']->photo_profile) }}" alt="{{ $data['staff']->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-800">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($data['staff']->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-sm font-bold text-slate-850 dark:text-slate-250">{{ $data['staff']->name }}</span>
                                        <span class="block text-xs font-mono font-bold text-slate-400 dark:text-slate-500">{{ $data['staff']->staff_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                {{ $data['staff']->institution }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($data['status'] === 'Hadir')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                                        Hadir
                                    </span>
                                @elseif($data['status'] === 'Izin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900">
                                        Izin
                                    </span>
                                @elseif($data['status'] === 'Sakit')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900">
                                        Sakit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        Alpa
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $data['check_in'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-300 dark:text-slate-700' }}">
                                {{ $data['check_in'] ? $data['check_in']->checked_at->format('H:i:s') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $data['check_out'] ? 'text-amber-600 dark:text-amber-400' : 'text-slate-300 dark:text-slate-700' }}">
                                {{ $data['check_out'] ? $data['check_out']->checked_at->format('H:i:s') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $data['permit']->notes ?? $data['sick']->notes ?? $data['check_in']->notes ?? '' }}">
                                @if($data['permit'])
                                    📝 Izin: {{ $data['permit']->notes }}
                                @elseif($data['sick'])
                                    🤒 Sakit: {{ $data['sick']->notes }}
                                @elseif($data['check_in'] && $data['check_in']->notes)
                                    💬 {{ $data['check_in']->notes }}
                                @else
                                    -
                                @endif
                            </td>
                            @if(!auth()->user()->isK3())
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                @if($data['status'] === 'Alpa')
                                    <button @click="openManualWithStaff({{ $data['staff']->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-xs font-bold transition-all border border-amber-200 dark:border-amber-800/60 cursor-pointer">
                                        ⚡ Presensi
                                    </button>
                                @elseif($data['status'] === 'Hadir' && !$data['check_out'])
                                    <button @click="selectedStaffId = {{ $data['staff']->id }}; statusType = 'check_out'; showManualModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 text-xs font-bold transition-all border border-indigo-200 dark:border-indigo-800/60 cursor-pointer">
                                        🚪 Pulang
                                    </button>
                                @else
                                    <span class="text-xs text-slate-450 dark:text-slate-500 font-medium">Selesai</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center"><span class="text-2xl">📭</span></div>
                                <p class="text-sm font-semibold text-slate-850 dark:text-slate-205">Tidak ada karyawan yang terdaftar atau cocok dengan pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Pencatatan Manual -->
    <div x-show="showManualModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4" x-transition>
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6" @click.away="showManualModal = false">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pencatatan Kehadiran Manual</h3>
                <button @click="showManualModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form :action="'/admin/staffs/' + selectedStaffId + '/attendance'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pegawai / Karyawan</label>
                    <select x-model="selectedStaffId" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($allStaffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->staff_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Kehadiran</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label :class="statusType === 'check_in' ? 'border-emerald-500 ring-2 ring-emerald-500/25 bg-emerald-50/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'" class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                            <input type="radio" name="status" value="check_in" x-model="statusType" class="sr-only">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">✅ Masuk</span>
                        </label>
                        <label :class="statusType === 'check_out' ? 'border-indigo-500 ring-2 ring-indigo-500/25 bg-indigo-50/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'" class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                            <input type="radio" name="status" value="check_out" x-model="statusType" class="sr-only">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">🚪 Pulang</span>
                        </label>
                        <label :class="statusType === 'permit' ? 'border-amber-500 ring-2 ring-amber-500/25 bg-amber-50/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'" class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                            <input type="radio" name="status" value="permit" x-model="statusType" class="sr-only">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">📝 Izin</span>
                        </label>
                        <label :class="statusType === 'sick' ? 'border-rose-500 ring-2 ring-rose-500/25 bg-rose-50/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'" class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                            <input type="radio" name="status" value="sick" x-model="statusType" class="sr-only">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">🤒 Sakit</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Shift Kerja</label>
                    <select name="shift_id" :required="statusType === 'check_in'" :disabled="statusType !== 'check_in'" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500 disabled:opacity-50">
                        <option value="">Pilih Shift...</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alasan / Keterangan</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        :required="statusType === 'permit' || statusType === 'sick'" 
                        :placeholder="statusType === 'check_in' || statusType === 'check_out' ? 'Tuliskan catatan opsional (misal: masuk lembur, pulang cepat)...' : 'Tuliskan alasan keterangan wajib (misal: sakit demam, keperluan dinas)...'" 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500"
                    ></textarea>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showManualModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 hover:from-brand-600 hover:to-indigo-700 text-white text-sm font-semibold shadow-lg shadow-brand-500/25 transition-all">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
