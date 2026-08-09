@extends('layouts.mobile')

@section('title', 'Scanner Akses Ruangan')

@section('content')
<div class="min-h-screen flex flex-col bg-slate-950 text-white" x-data="roomAccessApp()">
    {{-- Top Header --}}
    <div class="flex items-center justify-between px-6 py-4 bg-slate-900/90 backdrop-blur-xl border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <span class="text-xl">🚪</span>
            </div>
            <div>
                <h1 class="text-base font-bold text-white">Scanner Akses Ruangan</h1>
                <p class="text-xs text-slate-400">Monitoring Keberadaan Karyawan di Dalam Ruangan</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.occupancy.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 transition-colors">
                ← Kembali ke Monitoring
            </a>
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Akses Ruangan Mode</span>
            </div>
        </div>
    </div>

    {{-- Main Workspace Split Panel --}}
    <div class="flex-1 max-w-5xl mx-auto w-full p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
        
        {{-- Left Side: Camera / QR Scanner --}}
        <div class="flex flex-col p-6 bg-slate-900/60 border border-slate-800 rounded-3xl shadow-2xl w-full justify-between gap-4">
            
            {{-- Method Tabs --}}
            <div class="flex gap-2 p-1.5 bg-slate-800/80 rounded-2xl border border-slate-700/50">
                <button type="button" @click="setMethod('FACE')" 
                        :class="activeTab === 'FACE' ? 'bg-emerald-500 text-white font-bold shadow-md shadow-emerald-500/20' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2.5 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Pindai Wajah
                </button>
                <button type="button" @click="setMethod('QR')" 
                        :class="activeTab === 'QR' ? 'bg-emerald-500 text-white font-bold shadow-md shadow-emerald-500/20' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2.5 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    QR Code / NIP
                </button>
            </div>

            {{-- Face Recognition Video Container --}}
            <div x-show="activeTab === 'FACE'" class="relative aspect-square w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 flex items-center justify-center shadow-inner group">
                <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover scale-x-[-1]"></video>
                
                {{-- Face Target Box --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-56 h-72 border-2 border-dashed border-emerald-400/70 rounded-3xl animate-pulse flex items-center justify-center">
                        <div class="text-[10px] text-emerald-400 font-mono bg-slate-950/80 px-3 py-1 rounded-full border border-emerald-500/30">Posisikan Wajah Di Sini</div>
                    </div>
                </div>

                {{-- Status Overlay --}}
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-md px-4 py-1.5 rounded-full border border-slate-700 text-xs text-slate-300 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span x-text="faceStatusText">Mendeteksi Wajah...</span>
                </div>
            </div>

            {{-- QR Scanner / Input Container --}}
            <div x-show="activeTab === 'QR'" class="flex-1 flex flex-col justify-center items-center p-6 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl">📱</div>
                <div class="text-center">
                    <h3 class="text-sm font-bold text-white">Scan QR Code / Masukkan NIP</h3>
                    <p class="text-xs text-slate-400 mt-1">Tempelkan QR Code ke kamera atau ketik Kode Pegawai / NIP di bawah ini</p>
                </div>
                
                <form @submit.prevent="submitQR()" class="w-full space-y-3">
                    <input type="text" x-model="qrInput" placeholder="Contoh: OS-0001" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-center text-sm font-mono font-bold text-white outline-none focus:border-emerald-500">
                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 font-bold text-xs text-white shadow-lg shadow-emerald-500/20 transition-all">
                        Proses Akses Ruangan
                    </button>
                </form>
            </div>

        </div>

        {{-- Right Side: Real-time Scan Result Panel --}}
        <div class="flex flex-col p-6 bg-slate-900/60 border border-slate-800 rounded-3xl shadow-2xl justify-between min-h-[400px]">
            
            {{-- Default Waiting State --}}
            <template x-if="!resultState">
                <div class="flex-1 flex flex-col items-center justify-center text-center p-6 space-y-4">
                    <div class="w-20 h-20 rounded-3xl bg-slate-800/80 border border-slate-700/50 flex items-center justify-center text-4xl animate-bounce">
                        🚪
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Siap Menerima Akses Ruangan</h3>
                        <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">Silakan posisikan wajah di depan kamera atau scan QR Code untuk mencatat akses masuk / keluar ruangan.</p>
                    </div>
                </div>
            </template>

            {{-- Success Result State --}}
            <template x-if="resultState === 'SUCCESS'">
                <div class="flex-1 flex flex-col items-center justify-center text-center p-6 space-y-4 animate-fade-in-up">
                    <div class="w-24 h-24 rounded-full border-4 flex items-center justify-center shadow-xl"
                         :class="resultData.action === 'ENTRY' ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400 shadow-emerald-500/30' : 'border-blue-500 bg-blue-500/20 text-blue-400 shadow-blue-500/30'">
                        <span class="text-4xl" x-text="resultData.action === 'ENTRY' ? '🟢' : '🚪'"></span>
                    </div>

                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase"
                              :class="resultData.action === 'ENTRY' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30'"
                              x-text="resultData.action === 'ENTRY' ? 'AKSES MASUK RUANGAN' : 'AKSES KELUAR RUANGAN'"></span>
                        
                        <h2 class="text-xl font-black text-white mt-2" x-text="resultData.staff.name"></h2>
                        <p class="text-xs font-mono text-slate-400 mt-0.5" x-text="resultData.staff.staff_code + ' — ' + resultData.staff.institution"></p>
                    </div>

                    <div class="w-full p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs text-slate-300 font-medium">
                        <span x-text="resultData.message"></span>
                    </div>
                </div>
            </template>

            {{-- Error Result State --}}
            <template x-if="resultState === 'ERROR'">
                <div class="flex-1 flex flex-col items-center justify-center text-center p-6 space-y-4 animate-fade-in-up">
                    <div class="w-20 h-20 rounded-full bg-rose-500/20 border-4 border-rose-500 text-rose-400 flex items-center justify-center text-3xl shadow-xl shadow-rose-500/20">
                        ✖
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-rose-400">Akses Ruangan Gagal</h3>
                        <p class="text-xs text-slate-400 mt-1" x-text="errorMessage"></p>
                    </div>
                </div>
            </template>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roomAccessApp', () => ({
        activeTab: 'FACE',
        faceStatusText: 'Siap Scan Wajah',
        qrInput: '',
        resultState: null, // null | 'SUCCESS' | 'ERROR'
        resultData: null,
        errorMessage: '',
        isProcessing: false,

        async init() {
            await this.startWebcam();
            this.startFaceLoop();
        },

        setMethod(method) {
            this.activeTab = method;
            this.resultState = null;
        },

        async startWebcam() {
            const video = document.getElementById('webcam');
            if (!video) return;

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } } 
                });
                video.srcObject = stream;
                this.faceStatusText = 'Mendeteksi Wajah...';
            } catch (err) {
                console.error("Kamera tidak dapat diakses:", err);
                this.faceStatusText = 'Kamera Tidak Tersedia';
            }
        },

        startFaceLoop() {
            setInterval(async () => {
                if (this.activeTab !== 'FACE' || this.isProcessing || this.resultState) return;

                const video = document.getElementById('webcam');
                if (!video || !video.videoWidth) return;

                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const photoBase64 = canvas.toDataURL('image/jpeg', 0.85);

                    this.faceStatusText = 'Memproses Wajah...';
                    await this.processAccess('FACE_RECOGNITION', { proof_photo: photoBase64 });
                } catch (e) {
                    console.error("Face detection error:", e);
                }
            }, 3000);
        },

        async submitQR() {
            if (!this.qrInput || this.isProcessing) return;
            await this.processAccess('QR_CODE', { qr_token: this.qrInput });
            this.qrInput = '';
        },

        async processAccess(method, extraData = {}) {
            this.isProcessing = true;

            try {
                const payload = {
                    method: method,
                    ...extraData
                };

                const response = await fetch("{{ route('admin.occupancy.scan.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    this.resultState = 'SUCCESS';
                    this.resultData = data;
                } else {
                    this.resultState = 'ERROR';
                    this.errorMessage = data.message || 'Gagal memproses akses ruangan.';
                }
            } catch (err) {
                this.resultState = 'ERROR';
                this.errorMessage = 'Terjadi kesalahan koneksi server.';
            } finally {
                setTimeout(() => {
                    this.resultState = null;
                    this.isProcessing = false;
                }, 3500);
            }
        }
    }));
});
</script>
@endpush
