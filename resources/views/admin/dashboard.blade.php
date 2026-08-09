@extends('layouts.app')

@section('title', 'Dashboard Monitoring Karyawan Overhaul')
@section('header', 'Dashboard Monitoring Karyawan Overhaul')

@section('content')
<div class="space-y-6">
    @if(isset($flaggedRecords) && $flaggedRecords->count() > 0)
    <x-alert-banner type="danger" :count="$flaggedRecords->count()" />
    @endif

    {{-- K3 Emergency Evacuation Panel --}}
    <div class="p-5 rounded-3xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="text-3xl animate-pulse">🚨</span>
            <div>
                <h4 class="text-sm font-bold text-rose-700 dark:text-rose-400">K3 - Panel Evakuasi Darurat</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Unduh rekapitulasi data seluruh pekerja outsourcing yang saat ini berstatus aktif di dalam area unit PLTU untuk keperluan mitigasi penyelamatan.</p>
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('admin.evacuation.export.csv') }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-rose-600/15">
                📥 Ekspor CSV (Excel)
            </a>
            <a href="{{ route('admin.evacuation.export.json') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700 dark:border-slate-800">
                📥 Ekspor JSON
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-stat-card title="Populasi Aktif di Area" :value="$stats['active_onsite']" color="brand" id="stat-active" subtitle="Pegawai saat ini di dalam unit">
            <x-slot name="icon"><svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot>
        </x-stat-card>
        <x-stat-card title="Total Check-In Hari Ini" :value="$stats['total_check_ins']" color="emerald" id="stat-checkins" subtitle="Sejak pukul 00:00">
            <x-slot name="icon"><svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg></x-slot>
        </x-stat-card>
        <x-stat-card title="Total Check-Out Hari Ini" :value="$stats['total_check_outs']" color="amber" id="stat-checkouts" subtitle="Sejak pukul 00:00">
            <x-slot name="icon"><svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></x-slot>
        </x-stat-card>
        <x-stat-card title="Akses Mencurigakan" :value="$stats['flagged_count']" color="rose" id="stat-flagged" subtitle="Memerlukan verifikasi">
            <x-slot name="icon"><svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.072 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></x-slot>
        </x-stat-card>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between"><div><p class="text-sm text-slate-500 dark:text-slate-400">Face Recognition</p><p class="text-2xl font-bold mt-1 text-slate-800 dark:text-white" id="stat-face">{{ $stats['face_recognition_count'] }}</p></div><span class="text-2xl">👤</span></div>
            <div class="mt-3 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                @php $facePercent = ($stats['face_recognition_count'] + $stats['qr_code_count']) > 0 ? round($stats['face_recognition_count'] / ($stats['face_recognition_count'] + $stats['qr_code_count']) * 100) : 0; @endphp
                <div class="h-full bg-gradient-to-r from-brand-500 to-indigo-500 rounded-full transition-all duration-500" style="width: {{ $facePercent }}%"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between"><div><p class="text-sm text-slate-500 dark:text-slate-400">QR Code Fallback</p><p class="text-2xl font-bold mt-1 text-slate-800 dark:text-white" id="stat-qr">{{ $stats['qr_code_count'] }}</p></div><span class="text-2xl">📱</span></div>
            <div class="mt-3 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                @php $qrPercent = 100 - $facePercent; @endphp
                <div class="h-full bg-gradient-to-r from-violet-500 to-purple-500 rounded-full transition-all duration-500" style="width: {{ $qrPercent }}%"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="flex items-center justify-between"><div><p class="text-sm text-slate-500 dark:text-slate-400">Pegawai Unik Hari Ini</p><p class="text-2xl font-bold mt-1 text-slate-800 dark:text-white" id="stat-unique">{{ $stats['unique_staff_today'] }}</p></div><span class="text-2xl">📊</span></div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-lg">
            <h3 class="text-base font-semibold mb-4">Populasi per Jam</h3>
            <div class="relative" style="height: 280px;"><canvas id="hourlyChart"></canvas></div>
        </div>
        <div class="xl:col-span-2"><x-activity-log :logs="$recentLogs" /></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const REFRESH_INTERVAL = 10000;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let hourlyChartInstance = null;

async function refreshStats() {
    try {
        const response = await fetch('/api/dashboard/realtime', { 
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
            credentials: 'same-origin' 
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        const stats = data; 

        animateValue('stat-active', stats.active_onsite);
        animateValue('stat-checkins', stats.total_check_ins);
        animateValue('stat-checkouts', stats.total_check_outs);
        animateValue('stat-flagged', stats.flagged_count);
        animateValue('stat-face', stats.face_recognition_count);
        animateValue('stat-qr', stats.qr_code_count);
        animateValue('stat-unique', stats.unique_staff_today);
        
    } catch (err) { 
        console.error('Failed to refresh stats:', err); 
    }
}

async function refreshActivityLog() {
    try {
        const response = await fetch('/api/dashboard/activity-log?limit=5', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            credentials: 'same-origin'
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        const tbody = document.getElementById('activity-log-body');
        if (!tbody || !data.logs) return;
        
        if (data.logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500"><svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Belum ada aktivitas presensi hari ini</td></tr>';
            return;
        }
        
        tbody.innerHTML = data.logs.slice(0, 5).map(log => `
            <tr class="log-row border-b border-slate-50 dark:border-slate-800/50">
                <td class="px-6 py-3 text-sm font-mono text-slate-600 dark:text-slate-300">${log.checked_at}</td>
                <td class="px-6 py-3"><div><p class="text-sm font-semibold text-slate-800 dark:text-slate-200">${log.staff_name}</p><p class="text-xs text-slate-500">${log.staff_code}</p></div></td>
                <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">${log.institution}</td>
                <td class="px-6 py-3">${log.status === 'check_in' ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>MASUK</span>' : '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>KELUAR</span>'}</td>
                <td class="px-6 py-3"><span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-400">${log.method_icon || ''} ${log.method_label}</span></td>
                <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">${log.zone_name}</td>
                <td class="px-6 py-3">${log.is_flagged ? `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 text-xs font-semibold" title="${log.flag_reason || ''}">🚨 Flagged</span>` : '<span class="text-xs text-slate-400">✓</span>'}</td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Failed to refresh activity log:', err);
    }
}

async function refreshHourlyChart() {
    try {
        const response = await fetch('/api/dashboard/hourly-population', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            credentials: 'same-origin'
        });
        
        if (!response.ok) return;
        
        const json = await response.json();
        const rawData = json.data;
        
        if (hourlyChartInstance && rawData) {
            const checkIns = rawData.map(item => item.check_ins);
            const checkOuts = rawData.map(item => item.check_outs);
            
            hourlyChartInstance.data.datasets[0].data = checkIns;
            hourlyChartInstance.data.datasets[1].data = checkOuts;
            hourlyChartInstance.update();
        }
    } catch (err) {
        console.error('Failed to refresh hourly chart:', err);
    }
}

function animateValue(elementId, newValue) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const currentValue = parseInt(el.textContent) || 0;
    if (currentValue === newValue) return;
    el.textContent = newValue;
    el.style.transform = 'scale(1.1)';
    el.style.transition = 'transform 0.3s ease';
    setTimeout(() => { el.style.transform = 'scale(1)'; }, 300);
}

function initHourlyChart() {
    const ctx = document.getElementById('hourlyChart');
    if (!ctx) return;
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(148, 163, 184, 0.2)';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    
    hourlyChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
            datasets: [
                { label: 'Check-In', data: Array(24).fill(0), backgroundColor: 'rgba(52, 211, 153, 0.5)', borderColor: 'rgb(52, 211, 153)', borderWidth: 1, borderRadius: 4 },
                { label: 'Check-Out', data: Array(24).fill(0), backgroundColor: 'rgba(251, 113, 133, 0.5)', borderColor: 'rgb(251, 113, 133)', borderWidth: 1, borderRadius: 4 },
            ],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: textColor, usePointStyle: true, pointStyle: 'rectRounded', padding: 16, font: { size: 11, family: 'Inter' } } } },
            scales: { x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10, family: 'Inter' }, maxRotation: 45 } }, y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10, family: 'Inter' }, stepSize: 1 } } },
        },
    });
    
    // Initial fetch
    refreshHourlyChart();
}

setInterval(() => { 
    refreshStats(); 
    refreshActivityLog(); 
    refreshHourlyChart(); 
}, REFRESH_INTERVAL);

document.addEventListener('DOMContentLoaded', initHourlyChart);
</script>
@endpush
