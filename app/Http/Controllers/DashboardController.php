<?php

namespace App\Http\Controllers;

use App\Models\LogKeamanan;
use App\Models\UnitInstalasi;

class DashboardController extends Controller
{
    public function index()
    {
        $units = $this->hitungPopulasiPerUnit();
        $logTerbaru = LogKeamanan::with('pekerja.unitInstalasi')
            ->latest('waktu_akses')
            ->limit(20)
            ->get();

        return view('dashboard.index', compact('units', 'logTerbaru'));
    }

    /** Endpoint AJAX polling untuk update data real-time tanpa reload halaman */
    public function dataRealtime()
    {
        $logTerbaru = LogKeamanan::with('pekerja.unitInstalasi')
            ->latest('waktu_akses')
            ->limit(20)
            ->get()
            ->map(fn ($log) => [
                'nama'       => $log->pekerja->nama,
                'unit'       => $log->pekerja->unitInstalasi->nama_unit,
                'tipe_akses' => $log->tipe_akses,
                'metode'     => $log->metode,
                'waktu'      => $log->waktu_akses->format('d M Y H:i:s'),
            ]);

        return response()->json([
            'log_terbaru'       => $logTerbaru,
            'populasi_per_unit' => $this->hitungPopulasiPerUnit(),
        ]);
    }

    /**
     * Menghitung jumlah pekerja yang statusnya masih "masuk" (belum "keluar")
     * berdasarkan log terakhir masing-masing pekerja, per unit instalasi.
     */
    private function hitungPopulasiPerUnit()
    {
        return UnitInstalasi::all()->map(function ($unit) {
            $jumlahDiDalam = $unit->pekerja()
                ->get()
                ->filter(function ($pekerja) {
                    $logTerakhir = $pekerja->logKeamanan()->latest('waktu_akses')->first();
                    return $logTerakhir && $logTerakhir->tipe_akses === 'masuk';
                })
                ->count();

            return [
                'id' => $unit->id,
                'nama_unit' => $unit->nama_unit,
                'pekerja_di_dalam' => $jumlahDiDalam,
            ];
        });
    }
}