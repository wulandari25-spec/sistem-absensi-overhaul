
@extends('layouts.app')

@section('content')
<h4 class="mb-3">Dashboard Sistem Manajemen Keamanan — Populasi Pekerja Overhaul</h4>

<div class="row mb-4" id="kartuPopulasi">
    @foreach($units as $unit)
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow">
            <div class="card-body">
                <h6>{{ $unit['nama_unit'] }}</h6>
                <h2 class="fw-bold">{{ $unit['pekerja_di_dalam'] }}</h2>
                <small>Pekerja di dalam area saat ini</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow">
    <div class="card-header">Log Akses Terbaru (Real-time)</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Unit</th>
                    <th>Tipe Akses</th>
                    <th>Metode</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody id="tabelLog">
                @foreach($logTerbaru as $log)
                <tr>
                    <td>{{ $log->pekerja->nama }}</td>
                    <td>{{ $log->pekerja->unitInstalasi->nama_unit }}</td>
                    <td>
                        <span class="badge {{ $log->tipe_akses === 'masuk' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($log->tipe_akses) }}
                        </span>
                    </td>
                    <td>{{ $log->metode === 'face_recognition' ? 'Face Recognition' : 'QR Code' }}</td>
                    <td>{{ $log->waktu_akses->format('d M Y H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * Polling sederhana setiap 5 detik untuk menampilkan data "real-time".
 * Untuk kebutuhan skala lebih besar/production, pertimbangkan Laravel Echo + WebSocket (Pusher/Reverb).
 */
async function refreshDashboard() {
    const response = await fetch("{{ route('dashboard.realtime') }}");
    const data = await response.json();

    let htmlKartu = '';
    data.populasi_per_unit.forEach(unit => {
        htmlKartu += `
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h6>${unit.nama_unit}</h6>
                    <h2 class="fw-bold">${unit.pekerja_di_dalam}</h2>
                    <small>Pekerja di dalam area saat ini</small>
                </div>
            </div>
        </div>`;
    });
    document.getElementById('kartuPopulasi').innerHTML = htmlKartu;

    let htmlLog = '';
    data.log_terbaru.forEach(log => {
        const badgeClass = log.tipe_akses === 'masuk' ? 'bg-success' : 'bg-secondary';
        const metodeLabel = log.metode === 'face_recognition' ? 'Face Recognition' : 'QR Code';
        htmlLog += `
        <tr>
            <td>${log.nama}</td>
            <td>${log.unit}</td>
            <td><span class="badge ${badgeClass}">${log.tipe_akses}</span></td>
            <td>${metodeLabel}</td>
            <td>${log.waktu}</td>
        </tr>`;
    });
    document.getElementById('tabelLog').innerHTML = htmlLog;
}

setInterval(refreshDashboard, 5000);
</script>
@endpush