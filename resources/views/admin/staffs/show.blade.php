@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('header', 'Detail Pegawai')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>✅ {{ session('success') }}</div>
    @endif

    {{-- Action Buttons at the Top --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
        <a href="{{ route('admin.staffs.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
            ← Kembali
        </a>
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
                    @if($staff->is_face_registered)
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
            
            <div class="w-full border-t border-slate-100 dark:border-slate-800/80 my-4 pt-4 flex flex-col items-center gap-2">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">QR Code Pegawai</p>
                <div class="bg-white p-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-inner">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $staff->staff_code }}" alt="QR Code {{ $staff->name }}" class="w-28 h-28">
                </div>
                
                <div class="flex flex-col gap-1.5 w-full mt-1.5">
                    <button onclick="downloadQrOnly()" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-350 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer border border-transparent dark:border-slate-800">
                        📥 Unduh QR Code
                    </button>
                    <button onclick="printQrOnly()" class="w-full py-2 rounded-xl bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/20 dark:hover:bg-brand-950/40 text-brand-600 dark:text-brand-400 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        🖨️ Cetak Kartu ID / QR
                    </button>
                </div>
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
</div>

<script>
function downloadQrOnly() {
    const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $staff->staff_code }}";
    const filename = "QR_Code_{{ str_replace(' ', '_', $staff->name) }}.png";
    
    fetch(qrUrl)
        .then(response => response.blob())
        .then(blob => {
            const blobUrl = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(blobUrl);
        })
        .catch(err => {
            console.error("Gagal mengunduh QR Code:", err);
            window.open(qrUrl, '_blank');
        });
}

function printQrOnly() {
    const printWindow = window.open('', '_blank');
    const name = "{{ $staff->name }}";
    const staffCode = "{{ $staff->staff_code }}";
    const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(staffCode);
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cetak QR Code - ${name}</title>
            <style>
                body {
                    margin: 0;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: white;
                    color: black;
                }
                .card {
                    border: 2px solid #e2e8f0;
                    border-radius: 24px;
                    padding: 32px;
                    text-align: center;
                    width: 280px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    background: white;
                }
                .qr-img {
                    width: 220px;
                    height: 220px;
                    margin-bottom: 20px;
                }
                .name {
                    font-size: 20px;
                    font-weight: 800;
                    color: #1e293b;
                    margin: 0 0 6px 0;
                }
                .code {
                    font-size: 14px;
                    color: #64748b;
                    font-family: monospace;
                    font-weight: 700;
                    margin: 0;
                    letter-spacing: 0.5px;
                }
                @media print {
                    body {
                        height: auto;
                        background: none;
                    }
                    .card {
                        border: none;
                        box-shadow: none;
                        padding: 0;
                        margin: 40px auto;
                    }
                }
            </style>
        </head>
        <body>
            <div class="card">
                <img class="qr-img" src="${qrUrl}" alt="QR Code">
                <h3 class="name">${name}</h3>
                <p class="code">${staffCode}</p>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 500);
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection
