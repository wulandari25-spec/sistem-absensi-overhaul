@extends('layouts.app')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card shadow">
        <div class="card-body">
            <h4 class="mb-3">Pendaftaran Pekerja Outsourcing + Rekam Wajah</h4>

            <form method="POST" action="{{ route('pekerja.store') }}" id="formPekerja">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select" required>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Unit Instalasi</label>
                        <select name="unit_instalasi_id" class="form-select" required>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email (untuk login karyawan)</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password Awal</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                </div>

                <hr>
                <h5>Rekam Wajah Referensi</h5>
                <div class="text-center mb-2">
                    <video id="video" width="320" height="240" autoplay muted class="border rounded"></video>
                    <canvas id="canvas" width="320" height="240" class="d-none"></canvas>
                </div>
                <div class="text-center mb-3">
                    <button type="button" id="btnCapture" class="btn btn-secondary">Ambil & Proses Wajah</button>
                    <p id="statusWajah" class="mt-2 text-muted">Menunggu proses...</p>
                </div>

                <input type="hidden" name="foto_referensi" id="foto_referensi">
                <input type="hidden" name="face_descriptor" id="face_descriptor">

                <button type="submit" id="btnSimpan" class="btn btn-primary w-100" disabled>
                    Simpan Data Pekerja
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const btnCapture = document.getElementById('btnCapture');
const btnSimpan = document.getElementById('btnSimpan');
const statusWajah = document.getElementById('statusWajah');

async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
    video.srcObject = stream;
}

async function loadModels() {
    // Model weights face-api.js diletakkan di public/models (lihat Bagian 7.1)
    const MODEL_URL = '/models';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
}

btnCapture.addEventListener('click', async () => {
    statusWajah.textContent = 'Mendeteksi wajah...';

    const detection = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        statusWajah.textContent = 'Wajah tidak terdeteksi, coba lagi dengan pencahayaan lebih baik.';
        return;
    }

    // Ambil snapshot sebagai foto referensi (base64)
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const fotoBase64 = canvas.toDataURL('image/png');

    document.getElementById('foto_referensi').value = fotoBase64;
    document.getElementById('face_descriptor').value = JSON.stringify(Array.from(detection.descriptor));

    statusWajah.textContent = 'Wajah berhasil direkam. Silakan simpan data.';
    btnSimpan.disabled = false;
});

(async () => {
    await loadModels();
    await startCamera();
})();
</script>
@endpush