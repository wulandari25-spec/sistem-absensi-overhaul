@extends('layouts.mobile')

@section('title', 'Kiosk Presensi Wajah & QR')

@section('content')
<div class="min-h-screen flex flex-col" x-data="attendanceApp()">
    {{-- Top Header --}}
    <div class="flex items-center justify-between px-4 py-3 bg-slate-900/80 backdrop-blur-lg border-b border-slate-800/50">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/15">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div><h1 class="text-sm font-bold">Kiosk Presensi</h1><p class="text-xs text-slate-400">Sistem Kehadiran Overhaul</p></div>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-400">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-450 animate-pulse"></span>
                <span>Kiosk Mode</span>
            </div>
        </div>
    </div>

    {{-- Main Workspace Split Panel --}}
    <div class="flex-1 max-w-5xl mx-auto w-full p-4 grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
        
        {{-- Left Side: Camera / QR Scanner --}}
        <div class="flex flex-col p-6 bg-slate-900/40 border border-slate-850 rounded-3xl shadow-xl w-full justify-between gap-4">
            
            {{-- Tabs --}}
            <div class="flex gap-2 p-1 bg-slate-800/80 rounded-2xl">
                <button @click="switchTab('face')" :class="activeTab === 'face' ? 'bg-indigo-650 text-white shadow-lg shadow-indigo-650/30' : 'text-slate-400 hover:text-white'" class="flex-1 py-3 px-4 rounded-xl text-sm font-bold transition-all cursor-pointer">
                    👤 Pindai Wajah
                </button>
                <button @click="switchTab('qr')" :class="activeTab === 'qr' ? 'bg-indigo-650 text-white shadow-lg shadow-indigo-650/30' : 'text-slate-400 hover:text-white'" class="flex-1 py-3 px-4 rounded-xl text-sm font-bold transition-all cursor-pointer">
                    📱 QR Code
                </button>
            </div>

            {{-- Scanner View Area --}}
            <div class="flex-1 flex items-center justify-center py-2">
                {{-- Face Recognition Scanner --}}
                <div x-show="activeTab === 'face'" class="relative w-full max-w-sm aspect-[3/4] bg-slate-800 rounded-3xl overflow-hidden border-2 border-slate-650/40 shadow-2xl">
                    <video id="camera-feed" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-56 h-72 border-2 border-dashed border-indigo-400/50 rounded-[40%] relative">
                            <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-indigo-400 to-transparent scan-animation" x-show="isScanning || autoScanActive"></div>
                            <div class="absolute -top-1 -left-1 w-6 h-6 border-t-2 border-l-2 border-indigo-400 rounded-tl-xl"></div>
                            <div class="absolute -top-1 -right-1 w-6 h-6 border-t-2 border-r-2 border-indigo-400 rounded-tr-xl"></div>
                            <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-2 border-l-2 border-indigo-400 rounded-bl-xl"></div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-2 border-r-2 border-indigo-400 rounded-br-xl"></div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 inset-x-0 p-4 bg-gradient-to-t from-black/85 to-transparent">
                        <div class="text-center">
                            <p class="text-xs text-slate-350" x-text="statusMessage">Mendeteksi wajah otomatis...</p>
                        </div>
                    </div>
                </div>

                {{-- QR Code Scanner --}}
                <div x-show="activeTab === 'qr'" x-cloak class="w-full max-w-sm aspect-[3/4] rounded-3xl border border-slate-800 bg-slate-850 overflow-hidden flex flex-col justify-center relative">
                    <div id="qr-reader" class="w-full h-full"></div>
                </div>
            </div>

            {{-- Active scanning status message --}}
            <div class="text-center">
                <template x-if="activeTab === 'face'">
                    <span class="inline-flex items-center gap-2 text-xs text-emerald-400 font-semibold bg-emerald-500/10 px-3 py-1.5 rounded-full border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        Auto-Scanner Wajah Aktif
                    </span>
                </template>
                <template x-if="activeTab === 'qr'">
                    <span class="inline-flex items-center gap-2 text-xs text-indigo-400 font-semibold bg-indigo-500/10 px-3 py-1.5 rounded-full border border-indigo-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-ping"></span>
                        Arahkan QR ke Kamera
                    </span>
                </template>
            </div>

        </div>

        {{-- Right Side: Biodata Panel --}}
        <div class="flex flex-col p-8 bg-slate-900/60 border border-slate-800 rounded-3xl shadow-xl justify-center h-full min-h-[420px] w-full">
            
            {{-- State 1: Ready to Scan --}}
            <div x-show="!scannedStaff && !scanError" class="text-center py-12 space-y-4">
                <div class="w-24 h-24 mx-auto rounded-full bg-slate-850 flex items-center justify-center text-4xl border border-slate-850 shadow-inner">👤</div>
                <h3 class="text-lg font-bold text-slate-350">Siap Memindai</h3>
                <p class="text-xs text-slate-500 max-w-xs mx-auto leading-relaxed">Posisikan wajah Anda pada kamera atau arahkan QR Code untuk merekam kehadiran Masuk / Pulang secara otomatis.</p>
            </div>

            {{-- State 2: Scan Success (Biodata Lengkap) --}}
            <div x-show="scannedStaff" x-cloak class="space-y-5">
                {{-- Status Badge --}}
                <div class="flex justify-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                          :class="scannedStaff && scannedStaff.status_label === 'Masuk' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20'">
                        <span class="w-2 h-2 rounded-full" :class="scannedStaff && scannedStaff.status_label === 'Masuk' ? 'bg-emerald-400' : 'bg-indigo-400'"></span>
                        Absensi <span x-text="scannedStaff && scannedStaff.status_label"></span> Berhasil
                    </span>
                </div>

                {{-- Profile Image --}}
                <div class="flex justify-center">
                    <template x-if="scannedStaff && scannedStaff.photo_profile">
                        <img :src="scannedStaff.photo_profile" alt="Photo" class="w-24 h-24 rounded-full object-cover border-4 border-slate-855 shadow-xl">
                    </template>
                    <template x-if="scannedStaff && !scannedStaff.photo_profile">
                        <div class="w-24 h-24 rounded-full bg-slate-800 text-slate-400 border-4 border-slate-800 flex items-center justify-center text-3xl font-bold shadow-xl">
                            <span x-text="scannedStaff && scannedStaff.name.substr(0, 1).toUpperCase()"></span>
                        </div>
                    </template>
                </div>

                {{-- Biodata Lengkap --}}
                <div class="bg-slate-950/60 border border-slate-850 rounded-2xl p-4 text-left space-y-2">
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">Nama</span>
                        <span class="col-span-2 text-slate-200 font-bold" x-text="scannedStaff && scannedStaff.name"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">Kode Staf</span>
                        <span class="col-span-2 text-slate-300 font-mono font-bold" x-text="scannedStaff && scannedStaff.staff_code"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">NIK (KTP)</span>
                        <span class="col-span-2 text-slate-350" x-text="scannedStaff && scannedStaff.id_number"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">Vendor</span>
                        <span class="col-span-2 text-slate-350" x-text="scannedStaff && scannedStaff.institution"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">Unit Kerja</span>
                        <span class="col-span-2 text-slate-350" x-text="scannedStaff && scannedStaff.department"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">Jabatan</span>
                        <span class="col-span-2 text-slate-350" x-text="scannedStaff && scannedStaff.position"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-xs">
                        <span class="text-slate-500 font-semibold">Kontrak</span>
                        <span class="col-span-2 text-slate-400 font-mono" x-text="scannedStaff && scannedStaff.contract_period"></span>
                    </div>
                </div>

                {{-- Status Message --}}
                <p class="text-xs text-center text-slate-400" x-text="resultMessage"></p>

                {{-- Countdown progress bar --}}
                <div class="pt-2">
                    <div class="flex justify-between items-center text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                        <span>Status Kiosk</span>
                        <span>Mulai memindai kembali: <span class="text-slate-300 font-black" x-text="countdown"></span>s</span>
                    </div>
                    <div class="w-full bg-slate-850 h-1 rounded-full mt-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-400 to-indigo-500 h-full transition-all duration-1000" :style="'width: ' + (countdown * 10) + '%'"></div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button @click="resetScan()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-750 text-white font-bold text-xs transition-colors cursor-pointer border border-slate-700">
                        Lewati / Scan Selanjutnya ➡️
                    </button>
                </div>
            </div>

            {{-- State 3: Scan Failed --}}
            <div class="flex-col justify-center items-center py-8 space-y-4" x-show="scanError" x-cloak>
                <div class="w-16 h-16 mx-auto rounded-full bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-3xl text-rose-500 shadow-lg">❌</div>
                <div class="space-y-1 text-center">
                    <h3 class="text-base font-bold text-rose-405">Presensi Gagal</h3>
                    <p class="text-xs text-slate-500">Silakan ulangi kembali atau hubungi petugas</p>
                </div>
                <div class="p-3 bg-rose-500/5 border border-rose-500/10 rounded-xl text-center">
                    <p class="text-xs text-rose-350 leading-relaxed font-semibold" x-text="resultMessage"></p>
                </div>
                
                {{-- Countdown progress bar for error state --}}
                <div class="pt-2">
                    <div class="flex justify-between items-center text-[9px] font-bold text-rose-550 uppercase tracking-wider">
                        <span>Status Kiosk</span>
                        <span>Mulai ulang otomatis: <span class="text-rose-400 font-black" x-text="countdown"></span>s</span>
                    </div>
                    <div class="w-full bg-slate-850 h-1 rounded-full mt-1.5 overflow-hidden">
                        <div class="bg-rose-500 h-full transition-all duration-1000" :style="'width: ' + (countdown * 20) + '%'"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function attendanceApp() {
    return {
        activeTab: 'face',
        gpsStatus: 'loading',
        latitude: null, longitude: null,
        isScanning: false,
        autoScanActive: false,
        statusMessage: 'Posisikan wajah karyawan dalam bingkai',
        scannedStaff: null, scanError: false, resultMessage: '',
        countdown: 0, timer: null,
        faceStream: null,
        html5QrCode: null,
        autoScanTimer: null,

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
            this.switchTab('face');
        },

        requestGPS() {
            if (!navigator.geolocation) {
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
                    console.warn('GPS Error, using fallback:', err);
                    this.gpsStatus = 'fallback';
                    this.latitude = -6.200000;
                    this.longitude = 106.816666;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        async switchTab(tab) {
            this.activeTab = tab;
            this.resetScan();
            this.stopAllCameras();

            if (tab === 'face') {
                await this.initFaceCamera();
                this.startAutoScanLoop();
            } else if (tab === 'qr') {
                this.stopAutoScanLoop();
                this.initQrScanner();
            }
        },

        async initFaceCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
                });
                this.faceStream = stream;
                const video = document.getElementById('camera-feed');
                if (video) video.srcObject = stream;
            } catch (err) {
                this.statusMessage = 'Gagal mengakses kamera: ' + err.message;
            }
        },

        initQrScanner() {
            this.$nextTick(() => {
                const qrReader = document.getElementById('qr-reader');
                if (!qrReader) return;
                
                this.html5QrCode = new Html5Qrcode("qr-reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                this.html5QrCode.start(
                    { facingMode: "user" },
                    config,
                    (decodedText) => {
                        this.processQrAttendance(decodedText);
                    },
                    (errorMessage) => {
                        // Silent fail
                    }
                ).catch((err) => {
                    console.error("QR Code start failed:", err);
                });
            });
        },

        startAutoScanLoop() {
            this.autoScanActive = true;
            if (this.autoScanTimer) clearInterval(this.autoScanTimer);
            
            // Loop scanner wajah otomatis setiap 3 detik
            this.autoScanTimer = setInterval(async () => {
                if (this.isScanning || this.scannedStaff || this.scanError || this.activeTab !== 'face') {
                    return;
                }
                await this.startFaceScan();
            }, 3000);
        },

        stopAutoScanLoop() {
            this.autoScanActive = false;
            if (this.autoScanTimer) {
                clearInterval(this.autoScanTimer);
                this.autoScanTimer = null;
            }
        },

        async startFaceScan() {
            this.isScanning = true;
            this.statusMessage = 'Mendeteksi...';
            this.scannedStaff = null;
            this.scanError = false;

            try {
                const video = document.getElementById('camera-feed');
                if (!video || !video.videoWidth) {
                    this.isScanning = false;
                    return;
                }
                
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const base64Image = canvas.toDataURL('image/jpeg', 0.85);

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
                        latitude: this.latitude,
                        longitude: this.longitude,
                        proof_photo: base64Image,
                    })
                });

                const data = await response.json();
                this.isScanning = false;
                
                if (data.success) {
                    this.resultMessage = data.message || 'Presensi berhasil dicatat.';
                    this.scannedStaff = data.staff;
                    this.scanError = false;
                    this.startCountdown(10);
                } else {
                    this.resultMessage = data.message || 'Pencatatan gagal.';
                    this.scannedStaff = null;
                    this.scanError = true;
                    this.startCountdown(5);
                }
            } catch (err) {
                this.isScanning = false;
                this.resultMessage = 'Gagal verifikasi biometrik.';
                this.scannedStaff = null;
                this.scanError = true;
                this.startCountdown(5);
            }
        },

        async processQrAttendance(decodedText) {
            if (this.isScanning || this.scannedStaff || this.scanError) return;

            this.isScanning = true;
            this.scannedStaff = null;
            this.scanError = false;

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
                        latitude: this.latitude,
                        longitude: this.longitude,
                        qr_token: decodedText,
                    })
                });

                const data = await response.json();
                this.isScanning = false;

                if (data.success) {
                    this.resultMessage = data.message || 'Presensi QR berhasil.';
                    this.scannedStaff = data.staff;
                    this.scanError = false;
                    this.startCountdown(10);
                } else {
                    this.resultMessage = data.message || 'Pencatatan QR gagal.';
                    this.scannedStaff = null;
                    this.scanError = true;
                    this.startCountdown(5);
                }
            } catch (err) {
                this.isScanning = false;
                this.resultMessage = 'Gagal memproses QR Code.';
                this.scannedStaff = null;
                this.scanError = true;
                this.startCountdown(5);
            }
        },

        startCountdown(seconds) {
            this.countdown = seconds;
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(this.timer);
                    this.resetScan();
                }
            }, 1000);
        },

        stopAllCameras() {
            // Stop Face camera stream
            if (this.faceStream) {
                this.faceStream.getTracks().forEach(track => track.stop());
                this.faceStream = null;
            }
            const video = document.getElementById('camera-feed');
            if (video) video.srcObject = null;

            // Stop QR camera scanner
            if (this.html5QrCode) {
                if (this.html5QrCode.isScanning) {
                    this.html5QrCode.stop().then(() => {
                        this.html5QrCode = null;
                    }).catch(err => console.error("Error stopping QR:", err));
                } else {
                    this.html5QrCode = null;
                }
            }
        },

        resetScan() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
            this.statusMessage = 'Posisikan wajah karyawan dalam bingkai';
            this.isScanning = false;
            this.scannedStaff = null;
            this.scanError = false;
            this.resultMessage = '';
        }
    }
}
</script>
@endpush
