@extends('layouts.mobile')

@section('title', 'Form Pengajuan Izin/Sakit')

@section('content')
<div class="min-h-screen flex flex-col bg-slate-950 text-white pb-8">
    {{-- Header --}}
    <div class="sticky top-0 z-20 bg-slate-900/90 backdrop-blur-lg border-b border-slate-800/80 px-4 py-4 flex items-center gap-3">
        <a href="{{ route('attendance.history', $staff->id) }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors text-slate-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-sm font-bold tracking-tight">Form Pengajuan</h1>
            <p class="text-[10px] text-slate-400">Ajukan Izin / Sakit Mandiri</p>
        </div>
    </div>

    <div class="p-4 flex-1 max-w-md mx-auto w-full space-y-6">
        @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
            <p class="font-bold">Gagal mengirim pengajuan:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('attendance.permit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ statusType: 'permit' }">
            @csrf

            {{-- Type Toggles --}}
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Jenis Pengajuan</label>
                <div class="grid grid-cols-2 gap-4">
                    <label :class="statusType === 'permit' ? 'border-amber-500 ring-2 ring-amber-500/25 bg-amber-500/10' : 'border-slate-800 bg-slate-900/60'" class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer hover:bg-slate-800/50 transition-all duration-200">
                        <input type="radio" name="status" value="permit" x-model="statusType" class="sr-only">
                        <span class="text-2xl mb-1">📝</span>
                        <span class="text-sm font-bold text-slate-200">Izin</span>
                    </label>
                    <label :class="statusType === 'sick' ? 'border-rose-500 ring-2 ring-rose-500/25 bg-rose-500/10' : 'border-slate-800 bg-slate-900/60'" class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer hover:bg-slate-800/50 transition-all duration-200">
                        <input type="radio" name="status" value="sick" x-model="statusType" class="sr-only">
                        <span class="text-2xl mb-1">🤒</span>
                        <span class="text-sm font-bold text-slate-200">Sakit</span>
                    </label>
                </div>
            </div>

            {{-- Notes / Description --}}
            <div class="space-y-2">
                <label for="notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan / Alasan</label>
                <textarea 
                    name="notes" 
                    id="notes" 
                    rows="4" 
                    required 
                    placeholder="Tuliskan keterangan detail alasan Anda..."
                    class="w-full px-4 py-3 rounded-2xl bg-slate-900 border border-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                ></textarea>
            </div>

            {{-- Document Upload --}}
            <div class="space-y-2" x-show="statusType === 'sick'">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Surat Bukti (Foto Surat Dokter / Bukti Sakit)</label>
                <div class="relative group cursor-pointer">
                    <input 
                        type="file" 
                        name="proof_photo" 
                        accept="image/*"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    >
                    <div class="p-6 rounded-2xl border-2 border-dashed border-slate-800 bg-slate-900/40 text-center group-hover:border-slate-700 transition-colors">
                        <span class="text-3xl block mb-2">📁</span>
                        <span class="text-xs font-semibold text-slate-300">Pilih Foto atau Ambil Gambar</span>
                        <span class="text-[10px] text-slate-500 block mt-1">Format: JPG, PNG, WebP | Maksimal: 2MB</span>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button 
                    type="submit" 
                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-brand-500 to-indigo-600 hover:from-brand-600 hover:to-indigo-700 text-white font-bold text-base shadow-xl shadow-brand-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2"
                >
                    <span>Kirim Pengajuan</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
