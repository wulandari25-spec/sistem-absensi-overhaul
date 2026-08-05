@extends('layouts.app')

@section('content')
<style>
    /* Custom CSS - Versi Compact (Lebih Kecil & Padat) */
    .bg-main { background-color: #f4f7fe; }
    .card-modern {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 1rem;
    }
    .text-title {
        color: #2b3674;
        font-weight: 700;
        font-size: 1.15rem;
    }
    .text-subtitle {
        color: #a3aed1;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .form-label-custom {
        display: block !important;
        width: 100% !important;
        text-align: left !important;
        font-size: 0.75rem; 
        font-weight: 700;
        color: #8f9bba;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.3rem !important; 
    }
    
    .form-control-modern {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 8px;
        border: 1px solid #e0e5f2;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        background-color: #f4f7fe;
        color: #2b3674;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .form-control-modern::placeholder {
        color: #8f9bba;
        font-weight: 400;
    }
    .form-control-modern:focus {
        background-color: #ffffff;
        border-color: #4318ff;
        box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.1);
        outline: none;
    }
    
    .btn-primary-modern {
        background-color: #4318ff;
        color: white;
        border-radius: 8px;
        padding: 0.6rem 1.2rem;
        font-size: 0.875rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-primary-modern:hover { background-color: #3311cc; color: white; }
    .btn-primary-modern:disabled { opacity: 0.5; cursor: not-allowed; }
    
    .btn-outline-modern {
        display: inline-block;
        background-color: white;
        color: #2b3674;
        border: 1px solid #e0e5f2;
        border-radius: 8px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-outline-modern:hover { background-color: #f4f7fe; }
    
    .btn-light-modern {
        background-color: #e2e8f0;
        color: #2b3674;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.6rem;
        border: none;
        width: 100%;
        transition: all 0.2s;
    }
    .btn-light-modern:hover { background-color: #cbd5e1; }
    
    .btn-text-primary {
        display: block;
        width: 100%;
        text-align: center;
        color: #4318ff;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.4rem;
        text-decoration: none;
    }
    .btn-text-primary:hover { text-decoration: underline; }
    
    .divider-modern {
        height: 1px;
        background-color: #e0e5f2;
        margin: 1.5rem 0;
    }
</style>

<div class="container-fluid px-0">
    <div style="display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: center !important; width: 100% !important; margin-bottom: 1.5rem; gap: 1rem;">
        <div>
            <h1 class="text-title mb-1">Tambah Pegawai Outsourcing</h1>
            <p class="text-subtitle mb-0">Registrasi data personel baru untuk akses area</p>
        </div>
        <div style="margin-left: auto !important;">
            <a href="{{ route('admin.staffs.index') }}" class="btn btn-outline-modern" style="white-space: nowrap !important;">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-3" style="font-size: 0.875rem;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card card-modern h-100">
                <div class="card-body p-3 p-lg-4">
                    <h5 class="text-title mb-3 pb-2" style="font-size: 1rem; border-bottom: 1px solid #f4f7fe;">
                        <i class="fas fa-user-edit me-2 text-primary"></i>Input Data Manual
                    </h5>
                    
                    <form action="{{ route('admin.staffs.store') }}" method="POST" enctype="multipart/form-data" id="formStaff">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex flex-column w-100">
                                    <label for="name" class="form-label-custom">Nama Lengkap</label>
                                    <input type="text" class="form-control form-control-modern" id="name" name="name" value="{{ old('name') }}" placeholder="Misal: Ahmad Fauzi" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex flex-column w-100">
                                    <label for="staff_code" class="form-label-custom">Kode Staf / ID</label>
                                    <input type="text" class="form-control form-control-modern" id="staff_code" name="staff_code" value="{{ old('staff_code', $nextStaffCode ?? '') }}" placeholder="Misal: OS-0007" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex flex-column w-100">
                                    <label for="institution" class="form-label-custom">Instansi Asal</label>
                                    <input type="text" class="form-control form-control-modern" id="institution" name="institution" value="{{ old('institution') }}" placeholder="Misal: PT. Rekayasa Industri" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex flex-column w-100">
                                    <label for="department" class="form-label-custom">Departemen</label>
                                    <select class="form-control form-control-modern" id="department" name="department" required>
                                        <option value="" disabled selected>Pilih Departemen...</option>
                                        <option value="Mekanik Turbin" {{ old('department') == 'Mekanik Turbin' ? 'selected' : '' }}>Mekanik Turbin</option>
                                        <option value="Elektrikal" {{ old('department') == 'Elektrikal' ? 'selected' : '' }}>Elektrikal</option>
                                        <option value="Instrumen" {{ old('department') == 'Instrumen' ? 'selected' : '' }}>Instrumen</option>
                                        <option value="Scaffolding" {{ old('department') == 'Scaffolding' ? 'selected' : '' }}>Scaffolding</option>
                                        <option value="HSE" {{ old('department') == 'HSE' ? 'selected' : '' }}>HSE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex flex-column w-100">
                                    <label for="email" class="form-label-custom">Alamat Email Aktif</label>
                                    <input type="email" class="form-control form-control-modern" id="email" name="email" value="{{ old('email') }}" placeholder="Misal: ahmad.fauzi@email.com (opsional)">
                                    <div class="text-subtitle mt-1" style="font-size: 0.75rem;">Digunakan untuk pengiriman kredensial awal atau link pembuatan password akun (opsional).</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-flex flex-column w-100">
                                    <label for="position" class="form-label-custom">Posisi / Jabatan</label>
                                    <input type="text" class="form-control form-control-modern" id="position" name="position" value="{{ old('position') }}" placeholder="Misal: Supervisor Lapangan">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-flex flex-column w-100">
                                    <label for="phone" class="form-label-custom">Nomor Telepon</label>
                                    <input type="text" class="form-control form-control-modern" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Misal: 081234567890">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex flex-column w-100">
                                    <label for="id_number" class="form-label-custom">Nomor Identitas (NIK)</label>
                                    <input type="text" class="form-control form-control-modern" id="id_number" name="id_number" value="{{ old('id_number') }}" placeholder="Misal: 3201xxxxxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex flex-column w-100">
                                    <label for="contract_start_date" class="form-label-custom">Tanggal Mulai Kontrak (Opsional)</label>
                                    <input type="date" class="form-control form-control-modern" id="contract_start_date" name="contract_start_date" value="{{ old('contract_start_date') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex flex-column w-100">
                                    <label for="contract_end_date" class="form-label-custom">Tanggal Selesai Kontrak (Opsional)</label>
                                    <input type="date" class="form-control form-control-modern" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}">
                                </div>
                            </div>
                            <div class="col-12 mt-1">
                                <div class="text-subtitle" style="font-size: 0.75rem; color: #718096;">Masa kontrak payung (outsourcing) minimal adalah 20 hari dan maksimal 2 tahun.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex flex-column w-100 font-sans">
                                    <label for="password" class="form-label-custom">Kata Sandi (Password) Karyawan</label>
                                    <input type="password" class="form-control form-control-modern" id="password" name="password" placeholder="Ketik kata sandi (min. 8 karakter, opsional - karyawan bisa buat sendiri saat register)">
                                    <div class="text-subtitle mt-1" style="font-size: 0.75rem;">Kata sandi ini digunakan untuk masuk di Portal Presensi Karyawan.</div>
                                </div>
                            </div>
                        </div>

                        <div class="divider-modern"></div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex flex-column w-100">
                                    <label class="form-label-custom mb-3">REGISTRASI DATASET WAJAH (FACE DATA)</label>

                                    <div class="w-100 p-4 rounded-3 text-center" style="border: 2px dashed #cbd5e1; background-color: #f8fafc;">
                                        
                                        {{-- Opsi Registrasi Tabs --}}
                                        <div class="d-flex justify-content-center gap-2 mb-4" id="regModeTabs">
                                            <button type="button" id="tabUpload" class="btn btn-sm btn-primary-modern px-3 py-2" style="border-radius: 20px;">
                                                📁 Opsi 1: Unggah File Foto
                                            </button>
                                            <button type="button" id="tabCamera" class="btn btn-sm btn-outline-modern px-3 py-2" style="border-radius: 20px;">
                                                📷 Opsi 2: Ambil dari Kamera
                                            </button>
                                        </div>

                                        {{-- Tab 1: Upload File --}}
                                        <div id="uploadTabContainer" class="w-100">
                                            <div class="d-flex justify-content-center mb-3">
                                                <input type="file" class="form-control form-control-modern" id="photo_profile" name="photo_profile" accept="image/*" style="max-width: 320px;">
                                            </div>
                                        </div>

                                        {{-- Tab 2: Camera Capture --}}
                                        <div id="cameraTabContainer" class="w-100" style="display: none;">
                                            <div id="cameraContainer" class="mx-auto mb-3" style="width: 180px; height: 240px; border-radius: 12px; border: 2px solid #4318ff; overflow: hidden; background-color: #000; position: relative;">
                                                <video id="videoElement" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                                                <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-gradient-to-t from-black/50 to-transparent d-flex justify-content-center" style="z-index: 10;">
                                                    <button type="button" id="btnCapture" class="btn btn-sm btn-primary-modern py-1 px-3" style="font-size: 0.75rem;">
                                                        📸 Tangkap Gambar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-subtitle mb-3" style="font-size: 0.8rem;">Silakan posisikan wajah Anda tepat di tengah kamera.</div>
                                        </div>

                                        {{-- Common Preview Box --}}
                                        <div id="previewContainer" class="bg-white shadow-sm d-flex justify-content-center align-items-center mx-auto mb-3" style="width: 90px; height: 120px; border-radius: 8px; border: 1px solid #e0e5f2; overflow: hidden;">
                                            <img id="previewFoto" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                            <i id="iconCamera" class="fas fa-camera fa-2x" style="color: #a3aed1;"></i>
                                        </div>

                                        <p id="statusWajah" class="text-subtitle mb-0" style="font-size: 0.8rem; font-weight: 600;">Belum ada foto yang dipilih.</p>

                                        <p class="text-subtitle mx-auto mt-2 mb-0" style="font-size: 0.75rem; line-height: 1.6; max-width: 500px;">
                                            Dataset wajah akan dideteksi dan diekstrak secara otomatis oleh AI untuk verifikasi Check-In biometrik.
                                        </p>

                                        <input type="hidden" name="face_descriptor" id="face_descriptor">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 text-end" style="border-top: 1px solid #f4f7fe;">
                            <button type="submit" class="btn btn-primary-modern" id="btnSimpan">
                                <i class="fas fa-save me-2"></i>Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card card-modern h-100">
                <div class="card-body p-3 p-lg-4">
                    <h5 class="text-title mb-3 pb-2" style="font-size: 1rem; border-bottom: 1px solid #f4f7fe;">
                        <i class="fas fa-file-excel me-2" style="color: #05cd99;"></i>Import Massal
                    </h5>
                    
                    <p class="text-subtitle mb-4" style="font-size: 0.8rem; line-height: 1.5;">
                        Gunakan fitur ini untuk mendaftarkan banyak personel sekaligus. Registrasi wajah dapat dilakukan kemudian oleh masing-masing staf.
                    </p>

                    <form action="{{ route('admin.staffs.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 d-flex flex-column w-100">
                            <label for="excel_file" class="form-label-custom">Unggah File (.xlsx / .csv)</label>
                            <input type="file" class="form-control form-control-modern" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv" required>
                        </div>

                        <div class="d-flex flex-column gap-2 mt-3">
                            <button type="submit" class="btn btn-light-modern">
                                <i class="fas fa-upload me-2"></i>Mulai Import
                            </button>
                            
                            <a href="{{ route('admin.staffs.download-template') }}" class="btn btn-text-primary">
                                <i class="fas fa-download me-2"></i>Unduh Template Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
(function () {
    const fileInput = document.getElementById('photo_profile');
    const previewFoto = document.getElementById('previewFoto');
    const iconCamera = document.getElementById('iconCamera');
    const statusWajah = document.getElementById('statusWajah');
    const faceDescriptorInput = document.getElementById('face_descriptor');
    const btnSimpan = document.getElementById('btnSimpan');
    const formStaff = document.getElementById('formStaff');

    const tabUpload = document.getElementById('tabUpload');
    const tabCamera = document.getElementById('tabCamera');
    const uploadTabContainer = document.getElementById('uploadTabContainer');
    const cameraTabContainer = document.getElementById('cameraTabContainer');
    const btnCapture = document.getElementById('btnCapture');
    const previewContainer = document.getElementById('previewContainer');
    const videoElement = document.getElementById('videoElement');

    let localStream = null;
    let isProcessingFace = false;

    // Camera Actions
    async function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Browser Anda tidak mendukung akses kamera, atau halaman dibuka dalam konteks tidak aman (gunakan URL http://localhost:8000/ sebagai ganti http://127.0.0.1:8000/, atau gunakan koneksi HTTPS).');
            switchToUploadTab();
            return;
        }
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                video: { width: 480, height: 640, facingMode: 'user' }
            });
            videoElement.srcObject = localStream;
        } catch (err) {
            console.error('Error accessing camera:', err);
            
            let message = 'Tidak dapat mengakses kamera.';
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                message += '\n\nIzin kamera ditolak. Silakan aktifkan izin akses kamera pada ikon gembok/pengaturan situs di sebelah kiri alamat URL browser Anda.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                message += '\n\nPerangkat kamera tidak ditemukan pada komputer/laptop Anda.';
            } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                message += '\n\nKamera sedang digunakan oleh aplikasi lain (seperti Zoom, Teams, Google Meet, atau tab browser lain). Harap tutup aplikasi tersebut lalu coba lagi.';
            } else {
                message += '\n\nDetail error: ' + err.message;
            }
            
            alert(message);
            // Switch back to upload tab
            switchToUploadTab();
        }
    }

    function stopCamera() {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
        videoElement.srcObject = null;
    }

    function switchToUploadTab() {
        stopCamera();
        cameraTabContainer.style.display = 'none';
        uploadTabContainer.style.display = 'block';
        tabUpload.classList.replace('btn-outline-modern', 'btn-primary-modern');
        tabCamera.classList.replace('btn-primary-modern', 'btn-outline-modern');
    }

    function switchToCameraTab() {
        uploadTabContainer.style.display = 'none';
        cameraTabContainer.style.display = 'block';
        tabCamera.classList.replace('btn-outline-modern', 'btn-primary-modern');
        tabUpload.classList.replace('btn-primary-modern', 'btn-outline-modern');
        startCamera();
    }

    tabUpload.addEventListener('click', switchToUploadTab);
    tabCamera.addEventListener('click', switchToCameraTab);

    btnCapture.addEventListener('click', () => {
        if (!localStream) return;

        const canvas = document.createElement('canvas');
        canvas.width = 480;
        canvas.height = 640;
        const ctx = canvas.getContext('2d');

        // Mirror captured image to match live preview
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            if (!blob) return;

            const file = new File([blob], "photo_profile_captured.jpg", { type: "image/jpeg" });
            const container = new DataTransfer();
            container.items.add(file);
            fileInput.files = container.files;

            // Trigger change event to process face landmarks
            fileInput.dispatchEvent(new Event('change'));

            // Stop camera and switch back to upload tab to show the results
            switchToUploadTab();
        }, 'image/jpeg', 0.9);
    });

    let faceApiLoaded = false;

    async function waitForFaceApi() {
        return new Promise((resolve, reject) => {
            if (window.faceapi) {
                faceApiLoaded = true;
                return resolve();
            }
            let elapsed = 0;
            const check = setInterval(() => {
                elapsed += 100;
                if (window.faceapi) {
                    clearInterval(check);
                    faceApiLoaded = true;
                    resolve();
                } else if (elapsed > 5000) { // 5s timeout
                    clearInterval(check);
                    reject(new Error("Timeout loading face-api.js from CDN"));
                }
            }, 100);
        });
    }

    async function loadModels() {
        try {
            await waitForFaceApi();
            const MODEL_URL = '/models';
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);
        } catch (err) {
            console.warn('Face-API initialization failed:', err);
            faceApiLoaded = false;
        }
    }

    const modelsReady = loadModels();

    fileInput.addEventListener('change', async () => {
        const file = fileInput.files[0];
        if (!file) return;

        faceDescriptorInput.value = '';
        isProcessingFace = true;
        statusWajah.textContent = 'Memproses foto...';
        statusWajah.style.color = '';

        const objectUrl = URL.createObjectURL(file);
        previewFoto.src = objectUrl;
        previewFoto.style.display = 'block';
        iconCamera.style.display = 'none';

        try {
            await modelsReady;

            if (!window.faceapi || !faceApiLoaded) {
                statusWajah.textContent = '⚠️ Sistem deteksi wajah offline. Pegawai tetap dapat disimpan (Bypass).';
                statusWajah.style.color = '#e2a03f';
                return;
            }

            const img = await faceapi.bufferToImage(file);
            const detection = await faceapi
                .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                statusWajah.textContent = '⚠️ Wajah tidak terdeteksi di foto ini. Silakan gunakan foto lain yang lebih jelas.';
                statusWajah.style.color = '#f04438';
                return;
            }

            faceDescriptorInput.value = JSON.stringify(Array.from(detection.descriptor));
            statusWajah.textContent = '✓ Wajah berhasil terdeteksi dan didaftarkan.';
            statusWajah.style.color = '#05cd99';
        } catch (err) {
            console.error(err);
            statusWajah.textContent = '⚠️ Gagal memproses foto. Anda tetap dapat mencoba menyimpannya.';
            statusWajah.style.color = '#e2a03f';
        } finally {
            isProcessingFace = false;
        }
    });

    formStaff.addEventListener('submit', (e) => {
        if (isProcessingFace) {
            e.preventDefault();
            alert('Mohon tunggu, foto masih diproses...');
            return;
        }
        if (fileInput.files.length > 0 && !faceDescriptorInput.value) {
            const proceed = confirm('Wajah tidak terdeteksi di foto yang Anda masukkan. Apakah Anda ingin tetap menyimpan data pegawai tanpa verifikasi wajah otomatis? (Karyawan hanya bisa absen via QR Code / Manual)');
            if (!proceed) {
                e.preventDefault();
            }
        }
    });
})();
</script>
@endpush
@endsection 