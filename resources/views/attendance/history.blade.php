@extends('layouts.mobile')

@section('title', 'Riwayat Presensi Mingguan')

@section('content')
<div class="min-h-screen flex flex-col bg-slate-950 text-white pb-8">
    {{-- Header --}}
    <div class="sticky top-0 z-20 bg-slate-900/90 backdrop-blur-lg border-b border-slate-800/80 px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.check-in') }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-sm font-bold tracking-tight">Riwayat Presensi</h1>
                <p class="text-[10px] text-slate-400">Periode Mingguan</p>
            </div>
        </div>
        <span class="text-xs px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 border border-brand-500/10 font-semibold">
            Minggu Ini
        </span>
    </div>

    <div class="p-4 space-y-6 flex-1 max-w-md mx-auto w-full">
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            ✅ {{ session('success') }}
        </div>
        @endif

        {{-- Profile Card --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-800/60 rounded-3xl p-5 shadow-lg relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-brand-500/5 rounded-full blur-xl"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-slate-700 bg-slate-800/80 flex items-center justify-center text-xl font-bold">
                    @if($staff->photo_profile ?? false)
                        <img src="{{ asset('storage/'.$staff->photo_profile) }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($staff->name ?? '?', 0, 1)) }}
                    @endif
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-base tracking-tight">{{ $staff->name }}</h3>
                    <p class="text-xs font-mono text-slate-400">{{ $staff->staff_code }}</p>
                    <p class="text-[10px] text-slate-500 font-medium">{{ $staff->institution }} • {{ $staff->department ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3">
            <a href="{{ route('attendance.check-in') }}" class="flex-1 py-3 rounded-2xl bg-slate-900 border border-slate-800 hover:bg-slate-850 text-slate-200 text-xs font-bold text-center transition-colors">
                🏠 Portal Absensi
            </a>
            <a href="{{ route('attendance.permit') }}" class="flex-1 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 hover:bg-amber-500/20 text-amber-400 text-xs font-bold text-center transition-colors">
                📝 Ajukan Izin / Sakit
            </a>
        </div>

        {{-- QR Code Card --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-800/60 rounded-3xl p-5 shadow-lg flex flex-col items-center text-center relative overflow-hidden">
            <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-brand-500/5 rounded-full blur-xl"></div>
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">QR Code Presensi Cepat</h4>
            <div class="bg-white p-2.5 rounded-2xl shadow-inner mb-3">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ $staff->staff_code }}" alt="QR Code {{ $staff->name }}" class="w-32 h-32">
            </div>
            <p class="text-[10px] text-slate-400 font-medium leading-relaxed max-w-[240px]">
                Tunjukkan QR Code ini ke scanner petugas di gerbang masuk/keluar untuk melakukan absensi cepat.
            </p>
        </div>

        {{-- Weekly Status Tracker --}}
        <div class="bg-slate-900 border border-slate-800/80 rounded-3xl p-5 space-y-4">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Kehadiran</h4>
            
            @php
                $days = [
                    1 => ['name' => 'Sen', 'fullName' => 'Senin'],
                    2 => ['name' => 'Sel', 'fullName' => 'Selasa'],
                    3 => ['name' => 'Rab', 'fullName' => 'Rabu'],
                    4 => ['name' => 'Kam', 'fullName' => 'Kamis'],
                    5 => ['name' => 'Jum', 'fullName' => 'Jumat'],
                    6 => ['name' => 'Sab', 'fullName' => 'Sabtu'],
                    7 => ['name' => 'Min', 'fullName' => 'Minggu']
                ];
                
                // Group attendances by day number of the week (1 = Monday, 7 = Sunday)
                $groupedByDay = $attendances->groupBy(function($att) {
                    return $att->checked_at->format('N');
                });
            @endphp
            
            <div class="grid grid-cols-7 gap-2">
                @foreach($days as $num => $day)
                    @php
                        $dayLogs = $groupedByDay->get($num, collect());
                        $hasCheckIn = $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_IN);
                        $hasCheckOut = $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_OUT);
                        $hasPermit = $dayLogs->contains('status', \App\Enums\AttendanceStatus::PERMIT);
                        $hasSick = $dayLogs->contains('status', \App\Enums\AttendanceStatus::SICK);
                        
                        $bgClass = 'bg-slate-800/50 border-slate-800';
                        $iconColor = 'text-slate-600';
                        
                        if ($hasSick) {
                            $bgClass = 'bg-rose-500/20 border-rose-500/30';
                            $iconColor = 'text-rose-400';
                        } elseif ($hasPermit) {
                            $bgClass = 'bg-amber-500/20 border-amber-500/30';
                            $iconColor = 'text-amber-400';
                        } elseif ($hasCheckIn && $hasCheckOut) {
                            $bgClass = 'bg-emerald-500/20 border-emerald-500/30';
                            $iconColor = 'text-emerald-400';
                        } elseif ($hasCheckIn) {
                            $bgClass = 'bg-blue-500/20 border-blue-500/30';
                            $iconColor = 'text-blue-400';
                        }
                    @endphp
                    <div class="flex flex-col items-center p-2 rounded-xl border {{ $bgClass }} transition-all">
                        <span class="text-[10px] font-bold text-slate-400 mb-1.5">{{ $day['name'] }}</span>
                        <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $iconColor }} text-xs font-bold">
                            @if($hasSick)
                                S
                            @elseif($hasPermit)
                                I
                            @elseif($hasCheckIn && $hasCheckOut)
                                ✓✓
                            @elseif($hasCheckIn)
                                ✓
                            @else
                                -
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-500 pt-2 border-t border-slate-800/80">
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lengkap (In & Out)</div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Hanya Check-In</div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Izin (Manual)</div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Sakit (Manual)</div>
            </div>
        </div>

        {{-- Log Timeline --}}
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">Log Aktivitas Mingguan</h4>
            
            <div class="space-y-3">
                @forelse($attendances as $att)
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800/80 flex items-center justify-between gap-4 transition-all hover:border-slate-700/50">
                        <div class="flex items-center gap-3">
                            @php
                                $typeLabel = 'IN';
                                if ($att->status->value === 'check_out') $typeLabel = 'OUT';
                                elseif ($att->status->value === 'permit') $typeLabel = 'IZN';
                                elseif ($att->status->value === 'sick') $typeLabel = 'SKT';
                            @endphp
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-extrabold bg-{{ $att->status->color() }}-500/10 text-{{ $att->status->color() }}-400">
                                {{ $typeLabel }}
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-xs font-bold text-slate-200">
                                    {{ $att->status->label() }}
                                    @if(in_array($att->status->value, ['check_in', 'check_out']))
                                        — {{ $att->geofenceZone->zone_name ?? 'Luar Area' }}
                                    @endif
                                </p>
                                <p class="text-[10px] text-slate-500">
                                    {{ $att->checked_at->format('H:i') }} • {{ $att->checked_at->translatedFormat('l, d M') }}
                                    @if($att->notes)
                                        <span class="block text-slate-400 mt-1 italic font-normal text-[10px]">Catatan: "{{ $att->notes }}"</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end gap-1">
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-medium border border-slate-800/80">
                                {{ $att->method ? $att->method->icon() : '📝' }} {{ $att->method ? $att->method->label() : 'Manual' }}
                            </span>
                            @if($att->is_flagged)
                                <span class="text-[8px] px-1.5 py-0.5 rounded-full bg-rose-500/15 text-rose-400 font-bold border border-rose-500/10">
                                    ⚠️ Anomali
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center bg-slate-900 border border-slate-800/85 rounded-3xl">
                        <span class="text-3xl">📭</span>
                        <p class="text-xs font-semibold text-slate-400 mt-2">Belum ada riwayat presensi minggu ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom Action Button --}}
    <div class="sticky bottom-0 bg-slate-950/90 backdrop-blur-lg border-t border-slate-900 p-4 max-w-md mx-auto w-full">
        <a href="{{ route('attendance.check-in') }}" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 active:scale-[0.98] text-white font-bold text-sm tracking-wide shadow-xl shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
            <span>📷 Kembali ke Scanner Presensi</span>
        </a>
    </div>
</div>
@endsection
