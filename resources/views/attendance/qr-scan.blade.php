@extends('layouts.mobile')

@section('title', 'QR Code Scan - Fallback')

@section('content')
<div x-data="qrScanApp()" x-init="init()">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8">
        <div class="w-full max-w-sm text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-violet-500/20 flex items-center justify-center"><span class="text-4xl">📱</span></div>
            <h1 class="text-2xl font-bold mb-2">QR Code Fallback</h1>
            <p class="text-sm text-slate-400 mb-8">Metode cadangan ketika Face Recognition tidak tersedia</p>
            <div id="qr-reader-fullpage" class="w-full min-h-[250px] rounded-2xl border border-slate-600/50 bg-slate-800 mb-6 relative overflow-hidden"></div>
            <p class="text-xs mb-4" :class="statusColor" x-text="statusMessage"></p>
            <button @click="startScanner()" x-show="scannerFailed" class="w-full py-3 rounded-xl bg-violet-500 text-white text-sm font-semibold hover:bg-violet-600 transition-colors mb-4">
                🔄 Coba Nyalakan Kamera
            </button>
            <div class="space-y-3">
                <p class="text-xs text-slate-500">Arahkan kamera ke QR Code yang disediakan oleh petugas keamanan</p>
                <a href="{{ route('attendance.check-in') }}" class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition-colors">← Kembali ke Face Recognition</a>
            </div>
        </div>
    </div>

    <div x-show="showResult" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4" x-transition>
        <div class="w-full max-w-sm bg-slate-800 rounded-3xl border border-slate-700 p-8 text-center" x-transition>
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-4xl" :class="resultSuccess ? 'bg-emerald-500/20' : 'bg-red-500/20'">
                <span x-text="resultSuccess ? '✅' : '❌'"></span>
            </div>
            <h3 class="text-xl font-bold" x-text="resultSuccess ? 'Berhasil!' : 'Gagal'"></h3>
            <p class="text-sm text-slate-400 mt-2" x-text="resultMessage"></p>
            <button @click="showResult = false; startScanner()" class="w-full mt-6 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-semibold transition-colors text-xs">
                <span x-text="resultSuccess ? 'Scan Baru' : 'Coba Lagi'"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanApp() {
    return {
        mode: '{{ session("mode", "checkin") }}',
        qrScanner: null,
        activeStream: null,
        statusMessage: 'Menyiapkan scanner...',
        statusColor: 'text-slate-400',
        showResult: false,
        resultSuccess: false,
        resultMessage: '',
        scannerFailed: false,

        init() {
            this.$nextTick(() => {
                setTimeout(() => this.startScanner(), 300);
            });
        },

        async requestCameraPermission() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('NOT_SUPPORTED');
            }
            let stream = null;
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            } catch (_) {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
            }
            stream.getTracks().forEach(t => t.stop());
            return stream;
        },

        async startScanner() {
            this.stopAll();
            this.scannerFailed = false;
            this.statusMessage = 'Meminta izin kamera...';
            this.statusColor = 'text-amber-400';

            if (typeof Html5Qrcode === 'undefined') {
                this.statusMessage = 'Library scanner belum dimuat. Muat ulang halaman.';
                this.statusColor = 'text-red-400';
                this.scannerFailed = true;
                return;
            }

            try {
                this.statusMessage = 'Mengecek izin kamera...';
                await Promise.race([
                    this.requestCameraPermission(),
                    new Promise((_, reject) => setTimeout(() => reject(new Error('TIMEOUT')), 10000))
                ]);
            } catch (err) {
                console.error('Camera permission error:', err);
                let msg = 'Gagal mengakses kamera.';
                if (err.message === 'NOT_SUPPORTED') {
                    msg = 'Browser tidak mendukung akses kamera.';
                } else if (err.message === 'TIMEOUT') {
                    msg = 'Permintaan izin kamera timeout. Klik tombol di bawah untuk mencoba lagi.';
                } else if (err.name === 'NotAllowedError') {
                    msg = 'Izin kamera ditolak. Aktifkan izin kamera di pengaturan browser.';
                } else if (err.name === 'NotFoundError') {
                    msg = 'Kamera tidak ditemukan di perangkat ini.';
                } else if (err.name === 'NotReadableError') {
                    msg = 'Kamera sedang digunakan aplikasi lain.';
                }
                this.statusMessage = msg;
                this.statusColor = 'text-red-400';
                this.scannerFailed = true;
                return;
            }

            this.statusMessage = 'Menyalakan kamera scanner...';
            this.statusColor = 'text-amber-400';

            const el = document.getElementById('qr-reader-fullpage');
            if (!el) {
                this.statusMessage = 'Element scanner tidak ditemukan.';
                this.statusColor = 'text-red-400';
                this.scannerFailed = true;
                return;
            }

            el.innerHTML = '';

            this.qrScanner = new Html5Qrcode('qr-reader-fullpage');

            const configs = [
                { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 }
            ];

            let started = false;
            for (const config of configs) {
                try {
                    await this.qrScanner.start(
                        { facingMode: 'environment' },
                        config,
                        async (decodedText) => {
                            try { await this.qrScanner.stop(); } catch (_) {}
                            this.statusMessage = 'QR Terdeteksi! Memproses...';
                            this.statusColor = 'text-emerald-400';
                            this.processScan(decodedText);
                        },
                        () => {}
                    );
                    started = true;
                    break;
                } catch (err) {
                    console.warn('Environment camera failed, trying user camera:', err);
                    try {
                        await this.qrScanner.start(
                            { facingMode: 'user' },
                            config,
                            async (decodedText) => {
                                try { await this.qrScanner.stop(); } catch (_) {}
                                this.statusMessage = 'QR Terdeteksi! Memproses...';
                                this.statusColor = 'text-emerald-400';
                                this.processScan(decodedText);
                            },
                            () => {}
                        );
                        started = true;
                        break;
                    } catch (err2) {
                        console.warn('User camera also failed:', err2);
                    }
                }
            }

            if (started) {
                this.statusMessage = 'Arahkan kamera ke QR Code...';
                this.statusColor = 'text-emerald-400';
            } else {
                this.statusMessage = 'Gagal menyalakan kamera. Pastikan tidak ada aplikasi lain yang menggunakan kamera.';
                this.statusColor = 'text-red-400';
                this.scannerFailed = true;
            }
        },

        stopAll() {
            if (this.qrScanner) {
                this.qrScanner.stop().catch(() => {});
                this.qrScanner.clear().catch(() => {});
                this.qrScanner = null;
            }
            if (this.activeStream) {
                this.activeStream.getTracks().forEach(t => t.stop());
                this.activeStream = null;
            }
        },

        async processScan(scannedCode) {
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
                        scanned_code: scannedCode.trim(),
                    })
                });

                const data = await response.json();
                this.showResult = true;
                this.resultSuccess = data.success;
                this.resultMessage = data.message || 'Presensi gagal dicatat.';
            } catch (err) {
                this.showResult = true;
                this.resultSuccess = false;
                this.resultMessage = 'Gagal menghubungi server database.';
                console.error('QR process error:', err);
            }
        },
    };
}
</script>
@endpush
