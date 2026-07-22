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
                    <input 
                        type="text" 
                        id="institution" 
                        name="institution" 
                        value="{{ old('institution', $staff->institution) }}"
                        placeholder="Nama instansi/perusahaan"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 {{ $errors->has('institution') ? 'border-red-500' : '' }}"
                    >
                    @error('institution')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Departemen
                    </label>
                    <input 
                        type="text" 
                        id="department" 
                        name="department" 
                        value="{{ old('department', $staff->department) }}"
                        placeholder="Nama departemen (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                </div>

                <div>
                    <label for="position" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Posisi/Jabatan
                    </label>
                    <input 
                        type="text" 
                        id="position" 
                        name="position" 
                        value="{{ old('position', $staff->position) }}"
                        placeholder="Posisi kerja (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
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
                </div>

                <div>
                    <label for="contract_start_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Mulai Kontrak <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="contract_start_date" 
                        name="contract_start_date" 
                        value="{{ old('contract_start_date', $staff->contract_start_date ? $staff->contract_start_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                        required
                    >
                </div>

                <div>
                    <label for="contract_end_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Selesai Kontrak <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="contract_end_date" 
                        name="contract_end_date" 
                        value="{{ old('contract_end_date', $staff->contract_end_date ? $staff->contract_end_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                        required
                    >
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masa kontrak payung minimal 20 hari, maksimal 2 tahun.</p>
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
