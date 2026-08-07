@extends('layouts.app')

@section('title', 'Edit Pegawai Outsourcing')
@section('header', 'Edit Pegawai Outsourcing')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg p-6">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800">
                <div class="text-red-700 dark:text-red-300 text-sm font-medium mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc list-inside text-red-600 dark:text-red-400 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.staffs.update', $staff) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="staff_code" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Kode Pegawai <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="staff_code" 
                        name="staff_code" 
                        value="{{ old('staff_code', $staff->staff_code) }}"
                        placeholder="Misalnya: EMP001"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 {{ $errors->has('staff_code') ? 'border-red-500' : '' }}"
                    >
                    @error('staff_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $staff->name) }}"
                        placeholder="Nama lengkap pegawai"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 {{ $errors->has('name') ? 'border-red-500' : '' }}"
                    >
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Instansi <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="institution" 
                        name="institution" 
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 {{ $errors->has('institution') ? 'border-red-500' : '' }}"
                        required
                    >
                        <option value="" disabled>Pilih Instansi...</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->name }}" {{ old('institution', $staff->institution) == $inst->name ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                    @error('institution')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                <div>
                    <label for="department" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Departemen
                    </label>
                    <select 
                        id="department" 
                        name="department" 
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                        <option value="">Pilih Departemen...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ old('department', $staff->department) == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
 
                <div>
                    <label for="position" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Posisi/Jabatan
                    </label>
                    <select 
                        id="position" 
                        name="position" 
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                        <option value="">Pilih Posisi/Jabatan (Opsional)...</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->name }}" {{ old('position', $staff->position) == $pos->name ? 'selected' : '' }}>{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Nomor Telepon
                    </label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', $staff->phone) }}"
                        placeholder="Nomor telepon (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                </div>

                <div>
                    <label for="id_number" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Nomor Identitas (KTP/SIM)
                    </label>
                    <input 
                        type="text" 
                        id="id_number" 
                        name="id_number" 
                        value="{{ old('id_number', $staff->id_number) }}"
                        placeholder="Nomor identitas (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                </div>

                <div>
                    <label for="photo_profile" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Foto Profil
                    </label>
                    <input 
                        type="file" 
                        id="photo_profile" 
                        name="photo_profile" 
                        accept="image/*"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer file:mr-4 file:px-4 file:py-2 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100"
                    >
                    <p id="statusWajah" class="text-xs font-semibold text-slate-500 mt-2">Pilih foto wajah baru untuk memperbarui data biometrik.</p>
                    <input type="hidden" name="face_descriptor" id="face_descriptor" value="">
                </div>

                <div>
                    <label for="contract_start_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Mulai Kontrak (Opsional)
                    </label>
                    <input 
                        type="date" 
                        id="contract_start_date" 
                        name="contract_start_date" 
                        value="{{ old('contract_start_date', $staff->contract_start_date ? $staff->contract_start_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                </div>

                <div>
                    <label for="contract_end_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Selesai Kontrak (Opsional)
                    </label>
                    <input 
                        type="date" 
                        id="contract_end_date" 
                        name="contract_end_date" 
                        value="{{ old('contract_end_date', $staff->contract_end_date ? $staff->contract_end_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masa kontrak payung minimal 20 hari, maksimal 2 tahun (jika diisi).</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Reset Kata Sandi Karyawan
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ketik kata sandi baru (kosongkan jika tidak diubah)"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Minimal 8 karakter. Kosongkan jika tidak ingin mengganti.</p>
                </div>
            </div>

            @if($staff->photo_profile)
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Foto Profil Saat Ini</p>
                <img src="{{ asset('storage/' . $staff->photo_profile) }}" alt="{{ $staff->name }}" class="w-24 h-24 rounded-lg object-cover">
            </div>
            @endif

            <div class="flex items-center justify-between gap-4 pt-6 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('admin.staffs.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button 
                    type="submit" 
                    id="btnSubmit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 text-white text-sm font-semibold hover:from-brand-600 hover:to-indigo-700 shadow-lg shadow-brand-500/25 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Pegawai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
(function () {
    const fileInput = document.getElementById('photo_profile');
    const statusWajah = document.getElementById('statusWajah');
    const faceDescriptorInput = document.getElementById('face_descriptor');
    const form = document.querySelector('form');

    let faceApiLoaded = false;
    let isProcessingFace = false;

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
                } else if (elapsed > 5000) {
                    clearInterval(check);
                    reject(new Error("Timeout loading face-api.js"));
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

        try {
            await modelsReady;

            if (!window.faceapi || !faceApiLoaded) {
                statusWajah.textContent = '⚠️ Sistem deteksi wajah offline. Perubahan foto tetap dapat disimpan tanpa data biometrik baru.';
                statusWajah.style.color = '#e2a03f';
                return;
            }

            const img = await faceapi.bufferToImage(file);
            const detection = await faceapi
                .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                statusWajah.textContent = '⚠️ Wajah tidak terdeteksi di foto baru ini. Silakan gunakan foto lain yang lebih jelas.';
                statusWajah.style.color = '#f04438';
                return;
            }

            faceDescriptorInput.value = JSON.stringify(Array.from(detection.descriptor));
            statusWajah.textContent = '✓ Wajah baru berhasil terdeteksi dan didaftarkan.';
            statusWajah.style.color = '#05cd99';
        } catch (err) {
            console.error(err);
            statusWajah.textContent = '⚠️ Gagal memproses foto baru. Anda tetap dapat mencoba menyimpannya.';
            statusWajah.style.color = '#e2a03f';
        } finally {
            isProcessingFace = false;
        }
    });

    form.addEventListener('submit', (e) => {
        if (isProcessingFace) {
            e.preventDefault();
            alert('Mohon tunggu, foto baru masih diproses...');
            return;
        }
        if (fileInput.files.length > 0 && !faceDescriptorInput.value) {
            const proceed = confirm('Wajah tidak terdeteksi di foto baru yang Anda masukkan. Apakah Anda ingin tetap menyimpan data pegawai tanpa memperbarui data biometrik wajah? (Karyawan hanya bisa absen via QR Code / Manual jika data wajah kosong)');
            if (!proceed) {
                e.preventDefault();
            }
        }
    });
})();
</script>
@endpush
