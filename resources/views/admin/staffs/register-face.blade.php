@extends('layouts.app')

@section('title', 'Registrasi Wajah Karyawan')
@section('header', 'Registrasi Wajah - ' . $staff->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="registerFaceApp()">
    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm">
        ⚠️ {{ $errors->first() }}
    </div>
    @endif

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.staffs.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Pegawai
        </a>
        <div class="text-xs text-slate-400 font-mono">ID: {{ $staff->staff_code }}</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Opsi 1: Pindai dengan Kamera (Webcam) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl overflow-hidden p-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold">👤</div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base">Opsi 1: Pindai Wajah</h3>
                        <p class="text-xs text-slate-400">Gunakan webcam/kamera laptop untuk memindai wajah langsung</p>
                    </div>
                </div>

                <div class="relative aspect-video w-full bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 flex items-center justify-center group">
                    <template x-if="!cameraActive">
                        <div class="text-center p-4">
                            <span class="text-3xl block mb-2">📹</span>
                            <button type="button" @click="startCamera()" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-semibold hover:bg-slate-700 transition-all">
                                Aktifkan Kamera
                            </button>
                        </div>
                    </template>
                    <video id="webcam" class="w-full h-full object-cover" autoplay playsinline muted x-show="cameraActive"></video>
                    <canvas id="photo-preview" class="absolute inset-0 w-full h-full object-cover bg-slate-900" x-show="photoCaptured"></canvas>
                </div>
            </div>

            <div class="mt-6">
                <template x-if="cameraActive && !photoCaptured">
                    <button type="button" @click="capturePhoto()" class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-brand-500/20 active:scale-[0.98] transition-all">
                        📸 Ambil Foto Wajah
                    </button>
                </template>
                <template x-if="photoCaptured">
                    <div class="flex gap-2">
                        <button type="button" @click="resetCamera()" class="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-2xl text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                            Ulangi Foto
                        </button>
                        <form action="{{ route('admin.staffs.register-face.store', $staff) }}" method="POST" class="flex-1 m-0">
                            @csrf
                            <input type="hidden" name="captured_image" :value="capturedBase64">
                            <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl text-xs font-bold shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all">
                                ✓ Simpan Wajah
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>

        <!-- Opsi 2: Upload File Foto -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl overflow-hidden p-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center font-bold">📂</div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base">Opsi 2: Unggah Foto Wajah</h3>
                        <p class="text-xs text-slate-400">Unggah file foto berformat JPG/PNG untuk diekstrak wajahnya</p>
                    </div>
                </div>

                <form id="upload-form" action="{{ route('admin.staffs.register-face.store', $staff) }}" method="POST" enctype="multipart/form-data" class="space-y-4 m-0">
                    @csrf
                    <div class="relative w-full aspect-video bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center p-4 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-800 transition-all group"
                         @click="document.getElementById('file-input').click()">
                        
                        <input type="file" id="file-input" name="uploaded_image" accept="image/*" class="hidden" @change="previewUploadedImage($event)">
                        
                        <!-- Preview Uploaded -->
                        <img id="upload-preview" class="absolute inset-0 w-full h-full object-cover rounded-2xl hidden bg-slate-900">

                        <div id="upload-placeholder" class="text-center">
                            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">🖼️</span>
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Pilih atau Drag Foto Wajah</span>
                            <span class="text-[10px] text-slate-400 block mt-1">Ukuran maks 5MB (JPG/PNG/WEBP)</span>
                        </div>
                    </div>

                    <button type="submit" id="btn-upload" disabled class="w-full py-3 bg-brand-500 hover:bg-brand-600 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-2xl text-sm font-bold shadow-lg shadow-brand-500/20 active:scale-[0.98] transition-all">
                        🚀 Unggah & Daftarkan Wajah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function registerFaceApp() {
    return {
        cameraActive: false,
        photoCaptured: false,
        capturedBase64: '',
        stream: null,

        async startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Browser tidak mendukung akses kamera.');
                return;
            }
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
                });
                this.cameraActive = true;
                this.$nextTick(() => {
                    const video = document.getElementById('webcam');
                    if (video) video.srcObject = this.stream;
                });
            } catch (err) {
                alert('Gagal mengakses kamera: ' + err.message);
                console.error(err);
            }
        },

        capturePhoto() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('photo-preview');
            if (!video || !canvas) return;

            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            this.capturedBase64 = canvas.toDataURL('image/jpeg', 0.9);
            this.photoCaptured = true;

            // Stop camera stream after capturing
            this.stopCameraStream();
        },

        resetCamera() {
            this.photoCaptured = false;
            this.capturedBase64 = '';
            this.startCamera();
        },

        stopCameraStream() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },

        previewUploadedImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById('upload-preview');
                const placeholder = document.getElementById('upload-placeholder');
                const btn = document.getElementById('btn-upload');

                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
                if (btn) {
                    btn.disabled = false;
                }
            };
            reader.readAsDataURL(file);
        }
    };
}
</script>
@endpush
