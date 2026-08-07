@extends('layouts.app')
 
@section('title', 'Kelola Master Data')
@section('header', 'Kelola Master Data')
 
@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: localStorage.getItem('masterActiveTab') || 'institutions',
    setActiveTab(tab) {
        this.activeTab = tab;
        localStorage.setItem('masterActiveTab', tab);
    },
    // Modal state
    showModal: false,
    modalTitle: '',
    actionUrl: '',
    nameValue: '',
    methodValue: 'POST',
    isEdit: false,
    openAddModal(type) {
        this.isEdit = false;
        this.methodValue = 'POST';
        this.nameValue = '';
        if (type === 'institution') {
            this.modalTitle = 'Tambah Instansi Asal';
            this.actionUrl = '{{ route("admin.master-data.institutions.store") }}';
        } else if (type === 'department') {
            this.modalTitle = 'Tambah Departemen';
            this.actionUrl = '{{ route("admin.master-data.departments.store") }}';
        } else if (type === 'position') {
            this.modalTitle = 'Tambah Posisi Jabatan';
            this.actionUrl = '{{ route("admin.master-data.positions.store") }}';
        }
        this.showModal = true;
    },
    openEditModal(type, id, name) {
        this.isEdit = true;
        this.methodValue = 'PUT';
        this.nameValue = name;
        if (type === 'institution') {
            this.modalTitle = 'Edit Instansi Asal';
            this.actionUrl = `/admin/master-data/institutions/${id}`;
        } else if (type === 'department') {
            this.modalTitle = 'Edit Departemen';
            this.actionUrl = `/admin/master-data/departments/${id}`;
        } else if (type === 'position') {
            this.modalTitle = 'Edit Posisi Jabatan';
            this.actionUrl = `/admin/master-data/positions/${id}`;
        }
        this.showModal = true;
    }
}">
    <!-- Alert Success -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
        ✅ {{ session('success') }}
    </div>
    @endif
 
    <!-- Validation Errors -->
    @if($errors->any())
    <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800">
        <ul class="list-disc list-inside text-red-600 dark:text-red-400 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
 
    <!-- Tab Navigation -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 gap-2">
        <button @click="setActiveTab('institutions')" 
                :class="activeTab === 'institutions' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'"
                class="px-4 py-3 text-sm transition-all focus:outline-none cursor-pointer">
            🏢 Instansi Asal
        </button>
        <button @click="setActiveTab('departments')" 
                :class="activeTab === 'departments' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'"
                class="px-4 py-3 text-sm transition-all focus:outline-none cursor-pointer">
            📁 Departemen
        </button>
        <button @click="setActiveTab('positions')" 
                :class="activeTab === 'positions' ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'"
                class="px-4 py-3 text-sm transition-all focus:outline-none cursor-pointer">
            💼 Posisi Jabatan
        </button>
    </div>
 
    <!-- Tab Panels -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
        
        <!-- Tab 1: Institutions -->
        <div x-show="activeTab === 'institutions'" class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Daftar Instansi Asal</h2>
                    <p class="text-xs text-slate-400 mt-1">Kelola data instansi/perusahaan penyedia jasa tenaga outsourcing.</p>
                </div>
                @if(!auth()->user()->isK3() && !auth()->user()->isSecurity())
                <button @click="openAddModal('institution')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 text-white text-xs font-semibold hover:from-brand-600 hover:to-indigo-700 shadow-lg shadow-brand-500/25 transition-all cursor-pointer">
                    ➕ Tambah Instansi
                </button>
                @endif
            </div>
            
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-xl">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3 w-16">No</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Nama Instansi</th>
                            <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3 w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($institutions as $index => $inst)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                            <td class="px-6 py-3.5 text-sm font-medium text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-3.5 text-sm font-semibold">{{ $inst->name }}</td>
                            <td class="px-6 py-3.5 text-right text-sm">
                                <div class="inline-flex items-center gap-2">
                                    @if(!auth()->user()->isK3() && !auth()->user()->isSecurity())
                                    <button @click="openEditModal('institution', '{{ $inst->id }}', '{{ addslashes($inst->name) }}')" class="p-1 text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded transition-colors cursor-pointer" title="Edit">
                                        ✏️
                                    </button>
                                    <form action="{{ route('admin.master-data.institutions.destroy', $inst->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus instansi {{ $inst->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded transition-colors cursor-pointer" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400">Belum ada data instansi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
 
        <!-- Tab 2: Departments -->
        <div x-show="activeTab === 'departments'" class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Daftar Departemen</h2>
                    <p class="text-xs text-slate-400 mt-1">Kelola data unit kerja/departemen tempat penugasan pegawai.</p>
                </div>
                @if(!auth()->user()->isK3() && !auth()->user()->isSecurity())
                <button @click="openAddModal('department')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 text-white text-xs font-semibold hover:from-brand-600 hover:to-indigo-700 shadow-lg shadow-brand-500/25 transition-all cursor-pointer">
                    ➕ Tambah Departemen
                </button>
                @endif
            </div>
            
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-xl">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3 w-16">No</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Nama Departemen</th>
                            <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3 w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($departments as $index => $dept)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                            <td class="px-6 py-3.5 text-sm font-medium text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-3.5 text-sm font-semibold">{{ $dept->name }}</td>
                            <td class="px-6 py-3.5 text-right text-sm">
                                <div class="inline-flex items-center gap-2">
                                    @if(!auth()->user()->isK3() && !auth()->user()->isSecurity())
                                    <button @click="openEditModal('department', '{{ $dept->id }}', '{{ addslashes($dept->name) }}')" class="p-1 text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded transition-colors cursor-pointer" title="Edit">
                                        ✏️
                                    </button>
                                    <form action="{{ route('admin.master-data.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen {{ $dept->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded transition-colors cursor-pointer" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400">Belum ada data departemen.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
 
        <!-- Tab 3: Positions -->
        <div x-show="activeTab === 'positions'" class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Daftar Posisi Jabatan</h2>
                    <p class="text-xs text-slate-400 mt-1">Kelola data jabatan/posisi tugas karyawan outsourcing.</p>
                </div>
                @if(!auth()->user()->isK3() && !auth()->user()->isSecurity())
                <button @click="openAddModal('position')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 text-white text-xs font-semibold hover:from-brand-600 hover:to-indigo-700 shadow-lg shadow-brand-500/25 transition-all cursor-pointer">
                    ➕ Tambah Posisi
                </button>
                @endif
            </div>
            
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-xl">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3 w-16">No</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Nama Posisi Jabatan</th>
                            <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3 w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($positions as $index => $pos)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                            <td class="px-6 py-3.5 text-sm font-medium text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-3.5 text-sm font-semibold">{{ $pos->name }}</td>
                            <td class="px-6 py-3.5 text-right text-sm">
                                <div class="inline-flex items-center gap-2">
                                    @if(!auth()->user()->isK3() && !auth()->user()->isSecurity())
                                    <button @click="openEditModal('position', '{{ $pos->id }}', '{{ addslashes($pos->name) }}')" class="p-1 text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded transition-colors cursor-pointer" title="Edit">
                                        ✏️
                                    </button>
                                    <form action="{{ route('admin.master-data.positions.destroy', $pos->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus posisi {{ $pos->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded transition-colors cursor-pointer" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-slate-400 font-medium">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400">Belum ada data posisi jabatan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 
    <!-- Unified Add/Edit Modal (AlpineJS controlled) -->
    <div x-cloak x-show="showModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition.opacity>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-md w-full overflow-hidden animate-fade-in-up" @click.away="showModal = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-800 dark:text-white" x-text="modalTitle"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    ✕
                </button>
            </div>
            
            <!-- Modal Form -->
            <form :action="actionUrl" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                
                <div class="p-6">
                    <div class="space-y-2">
                        <label for="modal_name_input" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Nama Master <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="modal_name_input"
                               name="name" 
                               x-model="nameValue"
                               required 
                               placeholder="Masukkan nama..."
                               class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-slate-800 dark:text-slate-100">
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-brand-500 to-indigo-600 text-white text-xs font-bold hover:from-brand-600 hover:to-indigo-700 shadow-md shadow-brand-500/20 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
