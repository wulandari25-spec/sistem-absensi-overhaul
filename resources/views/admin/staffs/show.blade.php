@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('header', 'Detail Pegawai')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showManualAttendanceModal: false }">
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>✅ {{ session('success') }}</div>
    @endif

    {{-- Action Buttons at the Top --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
        <a href="{{ route('admin.staffs.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
            ← Kembali
        </a>
        @if(!auth()->user()->isK3())
        <div class="flex flex-wrap gap-3">
            <button @click="showManualAttendanceModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-455 text-sm font-bold hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Pencatatan Manual
            </button>
            <a href="{{ route('admin.staffs.edit', $staff) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400 text-sm font-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form action="{{ route('admin.staffs.destroy', $staff) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin ingin menonaktifkan pegawai ini?')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-700 dark:text-red-400 text-sm font-bold hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Nonaktifkan
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kartu Info Utama -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg p-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Kode Pegawai</p>
                    <p class="text-2xl font-bold text-brand-600 dark:text-brand-400 font-mono">{{ $staff->staff_code }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $staff->is_active_onsite ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }} text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full {{ $staff->is_active_onsite ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $staff->is_active_onsite ? 'Di Area' : 'Di Luar' }}
                </span>
            </div>

            <div class="space-y-6">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</p>
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $staff->name }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Instansi</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $staff->institution }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Departemen</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $staff->department ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Posisi/Jabatan</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $staff->position ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nomor Telepon</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $staff->phone ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nomor Identitas</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $staff->id_number ?? '-' }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai Kontrak</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            {{ $staff->contract_start_date ? $staff->contract_start_date->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai Kontrak</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            {{ $staff->contract_end_date ? $staff->contract_end_date->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                </div>

                @if($staff->contract_start_date && $staff->contract_end_date)
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Masa Kontrak Aktif</p>
                    @php
                        $diff = $staff->contract_start_date->diffInDays($staff->contract_end_date) + 1;
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 text-xs font-semibold border border-blue-100 dark:border-blue-900">
                        ⏳ {{ $diff }} Hari Kerja (Outsourcing)
                    </span>
                </div>
                @endif

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status Face Data</p>
                    @if($staff->face_descriptor)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Terdaftar
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold">
                            Belum Terdaftar
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Foto Profil -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg p-6 flex flex-col items-center justify-center text-center">
            @if($staff->photo_profile)
                <img src="{{ asset('storage/' . $staff->photo_profile) }}" alt="{{ $staff->name }}" class="w-32 h-32 rounded-full object-cover mb-4 border-4 border-brand-100 dark:border-brand-950/50">
            @else
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-brand-400 to-indigo-500 flex items-center justify-center text-white text-5xl font-bold mb-4">
                    {{ substr($staff->name, 0, 1) }}
                </div>
            @endif
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $staff->name }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-mono font-bold">{{ $staff->staff_code }}</p>
            
            <div class="w-full border-t border-slate-100 dark:border-slate-800/80 my-4 pt-4 flex flex-col items-center">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">QR Code Pegawai</p>
                <div class="bg-white p-2 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-inner mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $staff->staff_code }}" alt="QR Code {{ $staff->name }}" class="w-28 h-28">
                </div>
                <button onclick="window.print()" class="text-xs text-brand-600 dark:text-brand-400 hover:text-brand-500 font-bold hover:underline flex items-center gap-1">
                    🖨️ Cetak Kartu ID / QR
                </button>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi Terbaru -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Riwayat Kehadiran Pegawai</h3>
            
            <form method="GET" action="{{ route('admin.staffs.show', $staff) }}" class="flex flex-wrap items-center gap-2">
                {{-- Dropdown Bulan --}}
                <select name="month" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                    <option value="">-- Pilih Bulan --</option>
                    @foreach([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ] as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>

                {{-- Dropdown Tahun --}}
                <select name="year" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-800 dark:text-slate-200 outline-none focus:border-brand-500">
                    <option value="">-- Pilih Tahun --</option>
                    @foreach([2025, 2026, 2027] as $y)
                        <option value="{{ $y }}" {{ request('year', 2026) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                    Filter
                </button>
                
                @if(request()->has('month') || request()->has('year'))
                    <a href="{{ route('admin.staffs.show', $staff) }}" class="px-3 py-1.5 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Waktu</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Tipe/Status Kehadiran</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Metode</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Keterangan / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr class="border-b border-slate-50 dark:border-slate-800/50">
                        <td class="px-6 py-3 text-sm font-medium text-slate-900 dark:text-white">
                            {{ $attendance->checked_at->translatedFormat('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">
                            <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold bg-{{ $attendance->status->color() }}-50 dark:bg-{{ $attendance->status->color() }}-950/50 text-{{ $attendance->status->color() }}-700 dark:text-{{ $attendance->status->color() }}-400 border border-{{ $attendance->status->color() }}-100 dark:border-{{ $attendance->status->color() }}-800">
                                {{ $attendance->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1">
                                <span>{{ $attendance->method ? $attendance->method->icon() : '📝' }}</span>
                                <span>{{ $attendance->method ? $attendance->method->label() : 'Manual' }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $attendance->notes }}">
                            {{ $attendance->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <div class="text-2xl mb-2">📭</div>
                            <p class="text-sm font-semibold">Tidak ada riwayat kehadiran ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Pencatatan Manual (Masuk/Izin/Sakit) -->
    <div x-show="showManualAttendanceModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4" x-transition>
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-6" @click.away="showManualAttendanceModal = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pencatatan Kehadiran Manual</h3>
                <button @click="showManualAttendanceModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.staffs.attendance.store', $staff) }}" method="POST" class="space-y-4" x-data="{ statusType: 'check_in' }">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Status Kehadiran</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label :class="statusType === 'check_in' ? 'border-emerald-500 ring-2 ring-emerald-500/25 bg-emerald-50/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800'" class="relative flex items-center justify-center p-3 rounded-xl border cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                            <input type="radio" name="status" value="check_in" x-model="statusType" class="sr-only">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">✅ Masuk</span>
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
                    <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alasan / Keterangan</label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        :required="statusType !== 'check_in'" 
                        :placeholder="statusType === 'check_in' ? 'Tuliskan catatan opsional (misal: masuk lembur, masuk telat)...' : 'Tuliskan alasan keterangan wajib (misal: sakit demam, keperluan dinas)...'" 
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    ></textarea>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showManualAttendanceModal = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 hover:from-brand-600 hover:to-indigo-700 text-white text-sm font-semibold shadow-lg shadow-brand-500/25 transition-all">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
