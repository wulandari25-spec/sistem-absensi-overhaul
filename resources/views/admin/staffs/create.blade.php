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
                                    <select class="form-control form-control-modern" id="institution" name="institution" required>
                                        <option value="" disabled {{ old('institution') ? '' : 'selected' }}>Pilih Instansi...</option>
                                        @foreach($institutions as $inst)
                                            <option value="{{ $inst->name }}" {{ old('institution') == $inst->name ? 'selected' : '' }}>{{ $inst->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
 
                            <div class="col-md-6">
                                <div class="d-flex flex-column w-100">
                                    <label for="department" class="form-label-custom">Departemen</label>
                                    <select class="form-control form-control-modern" id="department" name="department" required>
                                        <option value="" disabled {{ old('department') ? '' : 'selected' }}>Pilih Departemen...</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
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
                                    <select class="form-control form-control-modern" id="position" name="position">
                                        <option value="" {{ old('position') ? '' : 'selected' }}>Pilih Posisi/Jabatan (Opsional)...</option>
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->name }}" {{ old('position') == $pos->name ? 'selected' : '' }}>{{ $pos->name }}</option>
                                        @endforeach
                                    </select>
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

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex flex-column w-100 font-sans">
                                    <label for="photo_profile" class="form-label-custom">Foto Profil</label>
                                    <input type="file" class="form-control form-control-modern" id="photo_profile" name="photo_profile" accept="image/*">
                                    <div class="text-subtitle mt-1" style="font-size: 0.75rem;">Unggah foto profil pegawai (opsional).</div>
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


@endsection 