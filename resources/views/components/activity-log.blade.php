@props(['logs' => collect()])

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <h3 class="text-base font-semibold">Log Aktivitas Real-time</h3>
            <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse-live"></span>
                Auto-refresh 10s
            </span>
        </div>
        <button onclick="refreshActivityLog()" class="p-2 rounded-xl text-slate-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-950/50 transition-colors" title="Refresh">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800">
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Waktu</th>
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Pegawai</th>
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Instansi</th>
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Metode</th>
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Zona</th>
                    <th class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-6 py-3">Flag</th>
                </tr>
            </thead>
            <tbody id="activity-log-body">
                @forelse($logs as $log)
                <tr class="log-row border-b border-slate-50 dark:border-slate-800/50">
                    <td class="px-6 py-3 text-sm font-mono text-slate-600 dark:text-slate-300">{{ $log->checked_at->format('H:i:s') }}</td>
                    <td class="px-6 py-3"><div><p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $log->staff->name ?? '-' }}</p><p class="text-xs text-slate-500">{{ $log->staff->staff_code ?? '-' }}</p></div></td>
                    <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $log->staff->institution ?? '-' }}</td>
                    <td class="px-6 py-3">@if($log->status->value === 'check_in')<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>MASUK</span>@else<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>KELUAR</span>@endif</td>
                    <td class="px-6 py-3"><span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-400">{{ $log->method->icon() }} {{ $log->method->label() }}</span></td>
                    <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $log->geofenceZone->zone_name ?? '-' }}</td>
                    <td class="px-6 py-3">@if($log->is_flagged)<span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 text-xs font-semibold" title="{{ $log->flag_reason }}">🚨 Flagged</span>@else<span class="text-xs text-slate-400">✓</span>@endif</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500"><svg class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Belum ada aktivitas presensi hari ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
