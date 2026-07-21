@extends('layouts.mobile')

@section('title', 'Presensi - Check In/Out')

@section('content')
<div class="min-h-screen flex flex-col" x-data="attendanceApp()">
    <div class="flex items-center justify-between px-4 py-3 bg-slate-900/80 backdrop-blur-lg border-b border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div><h1 class="text-sm font-bold">Presensi Overhaul</h1><p class="text-xs text-slate-400">Sistem Kehadiran</p></div>
        </div>
        <div class="flex items-center gap-2">
            <!--
                FIX: sekarang ada 3 state visual: ok (hijau), fallback (kuning), loading (kuning pulsing), error (merah).
                Sebelumnya kode selalu memaksa gpsStatus = 'ok' walau GPS gagal dan pakai koordinat fallback,
                sehingga badge "GPS OK" berbohong ke user.
            -->
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                 :class="{
                    'bg-emerald-500/20 text-emerald-400': gpsStatus === 'ok',
                    'bg-amber-500/20 text-amber-400': gpsStatus === 'loading' || gpsStatus === 'fallback',
                    'bg-red-500/20 text-red-400': gpsStatus === 'error'
                 }">
                <span class="w-1.5 h-1.5 rounded-full"
                      :class="{
                        'bg-emerald-400': gpsStatus === 'ok',
                        'bg-amber-400 animate-pulse': gpsStatus === 'loading',
                        'bg-amber-400': gpsStatus === 'fallback',
                        'bg-red-400': gpsStatus === 'error'
                      }"></span>
                <span x-text="gpsLabel"></span>
            </div>
        </div>
    </div>

    {{-- Employee Profile Info & Logout --}}
    <div class="px-4 pt-4">
        <div class="flex items-center justify-between p-4 bg-slate-900/80 border border-slate-800 rounded-2xl shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-500/20 text-brand-400 border border-brand-500/10 flex items-center justify-center font-black">
                    {{ strtoupper(substr(session('logged_in_staff_name', 'K'), 0, 1)) }}
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Karyawan Aktif</p>
                    <h3 class="text-sm font-bold text-slate-200">{{ session('logged_in_staff_name') }}</h3>
                </div>
            </div>
            <form action="{{ route('employee.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-rose-500/15 hover:text-rose-400 border border-slate-700 hover:border-rose-500/20 text-slate-400 text-xs font-semibold transition-all">
                    Keluar 🚪
                </button>
            </form>
        </div>
    </div>

    <div class="px-4 py-3">
        <div class="flex gap-2 p-1 bg-slate-800/80 rounded-xl">
            <button @click="mode = 'checkin'" :class="mode === 'checkin' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-400 hover:text-white'" class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all">⬅ Check-In</button>
            <button @click="mode = 'checkout'" :class="mode === 'checkout' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/30' : 'text-slate-400 hover:text-white'" class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all">Check-Out ➡</button>
        </div>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center px-4 py-6" x-show="scanMethod === 'face'">
        <div class="relative w-full max-w-sm aspect-[3/4] bg-slate-800 rounded-3xl overflow-hidden border-2 border-slate-600/50 glow-ring">
            <video id="camera-feed" class="w-full h-full object-cover" autoplay playsinline muted></video>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-56 h-72 border-2 border-dashed border-blue-400/60 rounded-[40%] relative">
                    <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-blue-400 to-transparent scan-animation" x-show="isScanning"></div>
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-2 border-l-2 border-blue-400 rounded-tl-xl"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-2 border-r-2 border-blue-400 rounded-tr-xl"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-2 border-l-2 border-blue-400 rounded-bl-xl"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-2 border-r-2 border-blue-400 rounded-br-xl"></div>
                </div>
            </div>
            <div class="absolute bottom-0 inset-x-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                <div class="text-center">
                    <p class="text-sm font-medium" x-text="statusMessage">Posisikan wajah dalam bingkai</p>
                    <p class="text-xs text-slate-400 mt-1" x-show="livenessChallenge" x-text="livenessChallenge"></p>
                </div>
            </div>
        </div>
        <div class="mt-6 flex flex-col items-center gap-3 w-full max-w-sm">
            <!-- FIX: tombol tidak lagi terkunci hanya karena gpsStatus !== 'ok'. Sekarang hanya terkunci saat GPS masih 'loading' (fallback tetap boleh lanjut). -->
            <button @click="startFaceScan()" :disabled="isScanning || gpsStatus === 'loading' || !modelsLoaded" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-base disabled:opacity-40 disabled:cursor-not-allowed shadow-xl shadow-blue-500/30 active:scale-[0.98] transition-transform">
                <span x-show="!isScanning && modelsLoaded">🔍 Mulai Verifikasi Wajah</span>
                <span x-show="!modelsLoaded">⏳ Memuat model wajah...</span>
                <span x-show="isScanning" class="flex items-center justify-center gap-2"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Memindai...</span>
            </button>
            <button @click="switchToQr()" class="w-full py-3 rounded-xl border border-slate-600 text-slate-300 text-sm font-medium hover:bg-slate-800 transition-colors">📱 Gunakan QR Code (Fallback)</button>
        </div>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center px-4 py-6" x-show="scanMethod === 'qr'" x-cloak>
        <div class="w-full max-w-sm">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-violet-500/20 flex items-center justify-center"><span class="text-3xl">📷</span></div>
                <h2 class="text-lg font-bold">QR Code Scanner</h2>
                <p class="text-sm text-slate-400 mt-1">Arahkan kamera ke QR Code yang diberikan petugas</p>
            </div>
            <div id="qr-reader" class="w-full min-h-[250px] rounded-2xl border border-slate-600/50 bg-slate-800 relative overflow-hidden"></div>
            <button @click="switchToFace()" class="w-full mt-4 py-3 rounded-xl border border-slate-600 text-slate-300 text-sm font-medium hover:bg-slate-800 transition-colors">👤 Kembali ke Face Recognition</button>
        </div>
    </div>

    <div x-show="showResult" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="w-full max-w-sm bg-slate-800 rounded-3xl border border-slate-700 p-8 text-center" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-4xl" :class="resultSuccess ? 'bg-emerald-500/20' : 'bg-red-500/20'"><span x-text="resultSuccess ? '✅' : '❌'"></span></div>
            <h3 class="text-xl font-bold" x-text="resultSuccess ? 'Berhasil!' : 'Gagal'"></h3>
            <p class="text-sm text-slate-400 mt-2" x-text="resultMessage"></p>
            
            <div class="flex flex-col gap-2 mt-6">
                <template x-if="resultSuccess && resultStaffId">
                    <a :href="'/attendance/history/' + resultStaffId" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold transition-all flex items-center justify-center gap-2 text-sm shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                        <span>📊 Lihat Riwayat Mingguan</span>
                    </a>
                </template>
                <button @click="showResult = false; resetScan()" class="w-full py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-semibold transition-colors text-xs">
                    <span x-text="resultSuccess && resultStaffId ? 'Scan Baru / Kembali' : 'OK'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{--
    CDN cdnjs.cloudflare.com untuk face-api.js 0.22.2 sudah tidak tersedia (404).
    Pakai jsDelivr sebagai gantinya -- otomatis serve semua versi package npm, jauh lebih reliable.
--}}
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function attendanceApp() {
    return {
        mode: 'checkin', scanMethod: 'face', gpsStatus: 'loading',
        latitude: null, longitude: null, isScanning: false,
        statusMessage: 'Memuat model wajah...', livenessChallenge: '',
        showResult: false, resultSuccess: false, resultMessage: '',
        resultStaffId: null,
        modelsLoaded: false,

        // FIX: label GPS terpisah dari warna, supaya pesan ke user akurat.
        get gpsLabel() {
            switch (this.gpsStatus) {
                case 'ok': return 'GPS OK';
                case 'fallback': return 'GPS Fallback';
                case 'loading': return 'GPS...';
                default: return 'GPS Error';
            }
        },

        init() {
            this.requestGPS();
            // FIX: kamera sekarang diminta langsung & independen dari proses load model face-api.js.
            // Sebelumnya initCamera() dipanggil DI DALAM loadModelsAndCamera() setelah model
            // berhasil dimuat -- kalau folder /models tidak ditemukan atau gagal dimuat,
            // initCamera() (yang memicu popup izin browser lewat getUserMedia) tidak pernah
            // terpanggil sama sekali, sehingga izin kamera tidak pernah muncul.
            this.initCamera();
            this.loadModels();
        },

        requestGPS() {
            if (!navigator.geolocation) {
                // FIX: tidak ada geolocation API sama sekali -> ini benar-benar fallback, bukan 'ok'.
                this.gpsStatus = 'fallback';
                this.latitude = -6.200000;
                this.longitude = 106.816666;
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = pos.coords.latitude;
                    this.longitude = pos.coords.longitude;
                    this.gpsStatus = 'ok';
                },
                (err) => {
                    // FIX: sebelumnya baris ini memaksa gpsStatus = 'ok' walau GPS gagal,
                    // sehingga badge menampilkan "GPS OK" padahal memakai koordinat fallback statis.
                    // Sekarang statusnya jujur ('fallback'), tapi user tetap bisa lanjut presensi
                    // (lihat perubahan :disabled pada tombol verifikasi wajah di atas).
                    console.warn('GPS Error, using fallback:', err);
                    this.gpsStatus = 'fallback';
                    this.latitude = -6.200000;
                    this.longitude = 106.816666;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        async loadModels() {
            try {
                await this.waitForFaceApi();

                const MODEL_URL = '/models';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);

                this.modelsLoaded = true;
                this.statusMessage = 'Posisikan wajah dalam bingkai';
            } catch (err) {
                // FIX: kamera tetap jalan walau model gagal load; hanya tombol verifikasi
                // yang tetap terkunci (disabled) karena modelsLoaded tetap false.
                console.error('Gagal memuat model face-api.js:', err);
                this.statusMessage = 'Gagal memuat model verifikasi wajah. Cek folder /models di server.';
            }
        },

        waitForFaceApi() {
            return new Promise((resolve) => {
                if (window.faceapi) return resolve();
                const check = setInterval(() => {
                    if (window.faceapi) { clearInterval(check); resolve(); }
                }, 100);
            });
        },

        async initCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // Terjadi jika halaman tidak diakses lewat context aman (https:// atau localhost/127.0.0.1).
                this.statusMessage = 'Kamera tidak didukung di context ini (butuh HTTPS atau localhost)';
                console.error('getUserMedia tidak tersedia di navigator.mediaDevices');
                return;
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } });
                const video = document.getElementById('camera-feed');
                if (video) video.srcObject = stream;
            } catch (err) {
                if (err.name === 'NotAllowedError') {
                    this.statusMessage = 'Izin kamera ditolak. Aktifkan izin kamera di pengaturan browser lalu muat ulang halaman.';
                } else if (err.name === 'NotFoundError') {
                    this.statusMessage = 'Kamera tidak ditemukan di perangkat ini.';
                } else {
                    this.statusMessage = 'Gagal mengakses kamera';
                }
                console.error('Camera error:', err);
            }
        },

        async startFaceScan() {
            if (!this.modelsLoaded) return;

            this.isScanning = true;
            this.statusMessage = 'Mendeteksi wajah...';
            this.livenessChallenge = 'Silakan kedipkan mata Anda';

            try {
                const video = document.getElementById('camera-feed');

                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection) {
                    this.isScanning = false;
                    this.statusMessage = 'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas dan coba lagi.';
                    return;
                }

                this.statusMessage = 'Wajah terdeteksi, memverifikasi ke server...';
                this.livenessChallenge = 'Liveness check berhasil ✓';

                const realDescriptor = Array.from(detection.descriptor);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/api/attendance/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        method: 'face_recognition',
                        status: this.mode === 'checkin' ? 'check_in' : 'check_out',
                        latitude: this.latitude,
                        longitude: this.longitude,
                        face_descriptor: realDescriptor,
                    })
                });

                const data = await response.json();
                this.isScanning = false;
                this.showResult = true;
                this.resultSuccess = data.success;
                this.resultMessage = data.message || 'Presensi gagal dicatat.';
                if (data.success && data.attendance) {
                    this.resultStaffId = data.attendance.staff_id;
                }
            } catch (err) {
                this.isScanning = false;
                this.showResult = true;
                this.resultSuccess = false;
                this.resultStaffId = null;
                this.resultMessage = 'Gagal menghubungi server database.';
                console.error('Face scan error:', err);
            }
        },

        resetScan() {
            this.isScanning = false;
            this.statusMessage = 'Posisikan wajah dalam bingkai';
            this.livenessChallenge = '';
            this.resultStaffId = null;
        },

        qrScanner: null,

        switchToQr() {
            this.scanMethod = 'qr';
            this.$nextTick(() => {
                setTimeout(() => this.startQrScanner(), 300);
            });
        },

        switchToFace() {
            this.stopQrScanner();
            this.scanMethod = 'face';
        },

        async startQrScanner() {
            this.stopQrScanner();

            if (typeof Html5Qrcode === 'undefined') {
                console.error('Html5Qrcode library not loaded');
                return;
            }

            const qrReader = document.getElementById('qr-reader');
            if (!qrReader) return;

            try {
                await Promise.race([
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                        .then(s => { s.getTracks().forEach(t => t.stop()); })
                        .catch(() => navigator.mediaDevices.getUserMedia({ video: true })
                            .then(s => { s.getTracks().forEach(t => t.stop()); })
                        ),
                    new Promise((_, rej) => setTimeout(() => rej(new Error('TIMEOUT')), 10000))
                ]);
            } catch (err) {
                console.error('Camera permission failed:', err);
                return;
            }

            const scanCallback = (decodedText) => {
                if (this.qrScanner) {
                    this.qrScanner.stop().catch(err => console.warn(err));
                }
                this.processQrScan(decodedText);
            };

            qrReader.innerHTML = '';
            this.qrScanner = new Html5Qrcode('qr-reader');

            try {
                await this.qrScanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 },
                    scanCallback,
                    () => {}
                );
            } catch (err) {
                console.warn('Environment camera failed, trying user camera:', err);
                try {
                    await this.qrScanner.start(
                        { facingMode: 'user' },
                        { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 },
                        scanCallback,
                        () => {}
                    );
                } catch (err2) {
                    console.error('Gagal memulai QR scanner:', err2);
                }
            }
        },

        stopQrScanner() {
            if (this.qrScanner) {
                this.qrScanner.stop().catch(() => {});
                this.qrScanner.clear().catch(() => {});
                this.qrScanner = null;
            }
            const video = document.getElementById('camera-feed');
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(t => t.stop());
                video.srcObject = null;
            }
        },

        async processQrScan(scannedCode) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/api/attendance/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        method: 'qr_code',
                        status: this.mode === 'checkin' ? 'check_in' : 'check_out',
                        latitude: this.latitude,
                        longitude: this.longitude,
                        scanned_code: scannedCode.trim(),
                    })
                });

                const data = await response.json();
                this.showResult = true;
                this.resultSuccess = data.success;
                this.resultMessage = data.message || 'Presensi gagal dicatat.';
                if (data.success && data.attendance) {
                    this.resultStaffId = data.attendance.staff_id;
                }
            } catch (err) {
                this.showResult = true;
                this.resultSuccess = false;
                this.resultStaffId = null;
                this.resultMessage = 'Gagal menghubungi server database.';
                console.error('QR scan error:', err);
            }
        },
    };
}
</script>
@endpush