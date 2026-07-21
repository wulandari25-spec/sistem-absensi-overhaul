@extends('layouts.app')

@section('content')
<div class="col-md-6 offset-md-3">
    <div class="card shadow">
        <div class="card-body text-center">
            <h4 class="mb-3">Presensi Karyawan Overhaul</h4>
            {{-- <p><strong>{{ $pekerja->nama }}</strong> — {{ $pekerja->unitInstalasi->nama_unit }}</p> --}}
            <p><strong>{{ $pekerja?->nama ?? 'Nama Belum Terdaftar' }}</strong> - {{ $pekerja?->unitInstalasi?->nama_unit ?? 'Unit Belum Diatur' }}</p>

            <div class="mb-3">
                <select id="tipeAkses" class="form-select w-50 mx-auto">
                    <option value="masuk">Masuk</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div id="modeFace">
                <video id="video" width="320" height="240" autoplay muted class="border rounded"></video>
                <p id="statusPresensi" class="mt-2 fw-bold text-muted">Memuat model deteksi wajah...</p>
                <button id="btnMulaiPresensi" class="btn btn-primary" disabled>Mulai Presensi (Wajah)</button>
            </div>

            <hr>
            <button id="btnFallbackQr" class="btn btn-outline-secondary btn-sm">
                Gagal? Gunakan QR Code sebagai gantinya
            </button>

            <div id="modeQr" class="d-none mt-3">
                <div id="qr-reader" class="mx-auto" style="width: 280px;"></div>
                <p id="statusQr" class="mt-2 text-muted"></p>
            </div>

            <div id="hasilAkhir" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>

const PEKERJA_ID = '{{ $pekerja?->id ?? "" }}';
const video = document.getElementById('video');
const statusPresensi = document.getElementById('statusPresensi');
const btnMulai = document.getElementById('btnMulaiPresensi');
const hasilAkhir = document.getElementById('hasilAkhir');
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ==== Kelas Liveness (ringkas dari Bagian 7.2) ====
class LivenessChallenge {
    constructor(videoEl, options = {}) {
        this.video = videoEl;
        this.threshold = options.threshold || 15;
        this.durasiDetikChallenge = options.durasi || 4;
    }
    pilihChallengeAcak() {
        const daftar = [
            { instruksi: 'Silakan hadap ke KIRI', arah: 'kiri' },
            { instruksi: 'Silakan hadap ke KANAN', arah: 'kanan' },
        ];
        return daftar[Math.floor(Math.random() * daftar.length)];
    }
    async ambilPosisiHidung() {
        const hasil = await faceapi.detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
        if (!hasil) return null;
        const noseTip = hasil.landmarks.getNose()[3];
        return { x: noseTip.x, y: noseTip.y };
    }
    async jalankan(onStatusUpdate) {
        const challenge = this.pilihChallengeAcak();
        onStatusUpdate(`${challenge.instruksi} (${this.durasiDetikChallenge} detik)`);
        const posisiAwal = await this.ambilPosisiHidung();
        if (!posisiAwal) return { live: false, alasan: 'Wajah tidak terdeteksi.' };
        await new Promise(r => setTimeout(r, this.durasiDetikChallenge * 1000));
        const posisiAkhir = await this.ambilPosisiHidung();
        if (!posisiAkhir) return { live: false, alasan: 'Wajah tidak terdeteksi di akhir.' };
        const pergeseranX = posisiAkhir.x - posisiAwal.x;
        const sesuaiArah =
            (challenge.arah === 'kiri' && pergeseranX < -this.threshold) ||
            (challenge.arah === 'kanan' && pergeseranX > this.threshold);
        return sesuaiArah ? { live: true } : { live: false, alasan: 'Gerakan tidak sesuai instruksi.' };
    }
}

// ==== Geolocation (dari Bagian 8.2) ====
function ambilLokasi() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) { reject('Geolocation tidak didukung.'); return; }
        navigator.geolocation.getCurrentPosition(
            (pos) => resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude }),
            (err) => reject('Gagal ambil lokasi: ' + err.message),
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
}

async function loadModels() {
    const MODEL_URL = '/models';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
}

async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
    video.srcObject = stream;
}

btnMulai.addEventListener('click', async () => {
    btnMulai.disabled = true;
    hasilAkhir.innerHTML = '';

    // 1. Liveness Detection
    const liveness = new LivenessChallenge(video);
    const hasilLiveness = await liveness.jalankan((pesan) => statusPresensi.textContent = pesan);

    if (!hasilLiveness.live) {
        statusPresensi.textContent = 'Liveness gagal: ' + hasilLiveness.alasan;
        btnMulai.disabled = false;
        return;
    }

    // 2. Deteksi & Ekstraksi Deskriptor Wajah
    statusPresensi.textContent = 'Memproses pengenalan wajah...';
    const deteksi = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!deteksi) {
        statusPresensi.textContent = 'Wajah tidak terdeteksi. Silakan coba lagi.';
        btnMulai.disabled = false;
        return;
    }

    // 3. Ambil Lokasi
    statusPresensi.textContent = 'Mengambil lokasi GPS...';
    let lokasi;
    try {
        lokasi = await ambilLokasi();
    } catch (e) {
        statusPresensi.textContent = e;
        btnMulai.disabled = false;
        return;
    }

    // 4. Kirim ke Server
    statusPresensi.textContent = 'Mengirim data presensi...';
    const response = await fetch("{{ route('presensi.face') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
            pekerja_id: PEKERJA_ID,
            face_descriptor: Array.from(deteksi.descriptor),
            liveness_valid: true,
            latitude: lokasi.latitude,
            longitude: lokasi.longitude,
            tipe_akses: document.getElementById('tipeAkses').value,
        }),
    });

    const hasil = await response.json();
    statusPresensi.textContent = hasil.pesan;
    hasilAkhir.innerHTML = hasil.status === 'sukses'
        ? `<div class="alert alert-success">${hasil.pesan}</div>`
        : `<div class="alert alert-danger">${hasil.pesan}</div>`;

    btnMulai.disabled = false;
});

// ==== Fallback QR Code ====
document.getElementById('btnFallbackQr').addEventListener('click', () => {
    document.getElementById('modeFace').classList.add('d-none');
    document.getElementById('modeQr').classList.remove('d-none');

    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 220 },
        async (decodedText) => {
            html5QrCode.stop();
            document.getElementById('statusQr').textContent = 'Memproses & mengambil lokasi...';

            let lokasi;
            try {
                lokasi = await ambilLokasi();
            } catch (e) {
                document.getElementById('statusQr').textContent = e;
                return;
            }

            const response = await fetch("{{ route('presensi.qr') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    token: decodedText,
                    latitude: lokasi.latitude,
                    longitude: lokasi.longitude,
                    tipe_akses: document.getElementById('tipeAkses').value,
                }),
            });

            const hasil = await response.json();
            document.getElementById('statusQr').textContent = hasil.pesan;
            hasilAkhir.innerHTML = hasil.status === 'sukses'
                ? `<div class="alert alert-success">${hasil.pesan}</div>`
                : `<div class="alert alert-danger">${hasil.pesan}</div>`;
        },
        () => {}
    );
});

(async () => {
    await loadModels();
    await startCamera();
    statusPresensi.textContent = 'Siap. Klik "Mulai Presensi" untuk memulai.';
    btnMulai.disabled = false;
})();
</script>
@endpush