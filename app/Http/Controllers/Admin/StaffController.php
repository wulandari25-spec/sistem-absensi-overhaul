<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Models\OutsourcingStaff;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StaffController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = $request->user();
                if ($user) {
                    if ($user->isK3() && in_array($request->route()->getActionMethod(), ['create', 'store', 'edit', 'update', 'destroy', 'import', 'storeManualAttendance'])) {
                        abort(403, 'Akses ditolak: Petugas K3 hanya diizinkan memantau data.');
                    }
                    if ($user->isSecurity() && in_array($request->route()->getActionMethod(), ['create', 'store', 'edit', 'update', 'destroy', 'import'])) {
                        abort(403, 'Akses ditolak: Petugas Keamanan hanya diizinkan memantau data dan melakukan pencatatan manual.');
                    }
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $query = OutsourcingStaff::registered();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        if ($institution = $request->input('institution')) {
            $query->where('institution', $institution);
        }

        if ($status = $request->input('status')) {
            if ($status === 'onsite') {
                $query->where('is_active_onsite', true);
            } elseif ($status === 'offsite') {
                $query->where('is_active_onsite', false);
            } elseif ($status === 'sick') {
                $query->whereHas('attendances', function ($q) {
                    $q->whereDate('checked_at', today())
                      ->where('status', 'sick');
                });
            } elseif ($status === 'permit') {
                $query->whereHas('attendances', function ($q) {
                    $q->whereDate('checked_at', today())
                      ->where('status', 'permit');
                });
            }
        }

        $staffs = $query->orderBy('name')->paginate(20);
        $institutions = OutsourcingStaff::registered()->distinct()->pluck('institution');

        return view('admin.staffs.index', compact('staffs', 'institutions'));
    }

    public function create()
    {
        return view('admin.staffs.create');
    }

    public function store(StoreStaffRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo_profile')) {
            $data['photo_profile'] = $request->file('photo_profile')->store('staff-photos', 'public');
        }

        // Hash password jika diisi
        if (!empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Decode face_descriptor dari JSON string menjadi array PHP
        if (!empty($data['face_descriptor'])) {
            $data['face_descriptor'] = json_decode($data['face_descriptor'], true);
        } else {
            unset($data['face_descriptor']);
        }

        OutsourcingStaff::create($data);

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Request $request, OutsourcingStaff $staff)
    {
        $query = $staff->attendances()->with('geofenceZone');

        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($selectedMonth && $selectedYear) {
            $startDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
            $endDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->format('Y-m-d');
        }

        if ($startDate && $endDate) {
            $query->whereBetween('checked_at', [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $attendances = $query->latest('checked_at')->paginate(15)->withQueryString();

        return view('admin.staffs.show', compact('staff', 'attendances', 'startDate', 'endDate'));
    }

    public function edit(OutsourcingStaff $staff)
    {
        return view('admin.staffs.edit', compact('staff'));
    }

    public function update(StoreStaffRequest $request, OutsourcingStaff $staff)
    {
        // Ambil data yang sudah divalidasi
        $data = $request->validated();

        // Cek apakah ada file foto baru
        if ($request->hasFile('photo_profile')) {
            $data['photo_profile'] = $request->file('photo_profile')->store('staff-photos', 'public');
        } else {
            // Jika tidak ada foto baru, hapus key ini agar tidak menimpa foto lama dengan null
            unset($data['photo_profile']);
        }

        // Hash password jika diisi, jika kosong hapus agar tidak menimpa password lama
        if (!empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Lakukan update
        $staff->update($data);

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(OutsourcingStaff $staff)
    {
        $staff->update(['is_registered' => false]);

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Pegawai berhasil dinonaktifkan.');
    }

    // Tambahkan di dalam StaffController.php
    public function import(Request $request)
    {
        // Logika import excel menggunakan Maatwebsite akan diletakkan di sini nanti
        return back()->with('success', 'Tombol import berhasil ditekan (fitur segera hadir).');
    }

    public function storeManualAttendance(Request $request, OutsourcingStaff $staff)
    {
        $validated = $request->validate([
            'status' => 'required|in:check_in,permit,sick',
            'notes' => 'required_if:status,permit,sick|nullable|string|max:500',
        ], [
            'status.required' => 'Pilih jenis status kehadiran.',
            'notes.required_if' => 'Keterangan/alasan wajib diisi untuk Izin atau Sakit.',
        ]);

        $status = $validated['status'];
        $notes = $validated['notes'] ?? 'Pencatatan kehadiran manual oleh administrator/petugas';

        // Buat data absensi manual
        $staff->attendances()->create([
            'status' => $status,
            'method' => 'manual',
            'notes' => $notes,
            'checked_at' => now(),
        ]);

        // Sesuaikan status onsite
        $isActiveOnsite = ($status === 'check_in');
        $staff->update([
            'is_active_onsite' => $isActiveOnsite,
            'last_seen_at' => now(),
        ]);

        $statusLabel = match($status) {
            'check_in' => 'Masuk',
            'permit' => 'Izin',
            'sick' => 'Sakit',
        };

        return back()->with('success', 'Status Kehadiran (' . $statusLabel . ') berhasil dicatat untuk ' . $staff->name);
    }

    public function exportEvacuationCsv()
    {
        $onsiteStaffs = OutsourcingStaff::registered()->where('is_active_onsite', true)->orderBy('name')->get();

        $filename = "k3_evakuasi_darurat_" . now()->format('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Kode Staf', 'Nama Lengkap', 'Instansi (Vendor)', 'Unit/Departemen', 'Jabatan', 'No. Telp', 'NIK/No. KTP', 'Waktu Masuk'];

        $callback = function() use($onsiteStaffs, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to open correctly in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';');

            foreach ($onsiteStaffs as $staff) {
                $lastCheckIn = $staff->attendances()
                    ->where('status', 'check_in')
                    ->latest('checked_at')
                    ->first();
                
                fputcsv($file, [
                    $staff->staff_code,
                    $staff->name,
                    $staff->institution,
                    $staff->department ?? '-',
                    $staff->position ?? '-',
                    $staff->phone ?? '-',
                    $staff->id_number ?? '-',
                    $lastCheckIn ? $lastCheckIn->checked_at->format('d/m/Y H:i') : '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportEvacuationJson()
    {
        $onsiteStaffs = OutsourcingStaff::registered()->where('is_active_onsite', true)->orderBy('name')->get();

        $data = $onsiteStaffs->map(function ($staff) {
            $lastCheckIn = $staff->attendances()
                ->where('status', 'check_in')
                ->latest('checked_at')
                ->first();

            return [
                'staff_code' => $staff->staff_code,
                'name' => $staff->name,
                'institution' => $staff->institution,
                'department' => $staff->department,
                'position' => $staff->position,
                'phone' => $staff->phone,
                'id_number' => $staff->id_number,
                'checked_in_at' => $lastCheckIn ? $lastCheckIn->checked_at->toIso8601String() : null,
            ];
        });

        $filename = "k3_evakuasi_darurat_" . now()->format('Y-m-d_H-i-s') . ".json";

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename=' . $filename);
    }

    public function downloadTemplate()
    {
        $filename = "template_import_karyawan.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['staff_code', 'name', 'institution', 'department', 'position', 'phone', 'id_number', 'email', 'password', 'contract_start_date', 'contract_end_date'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 for Excel
            fputcsv($file, $columns, ';');
            
            // Contoh baris
            fputcsv($file, ['OS-0001', 'Ahmad Fauzi', 'PT. Rekayasa Industri', 'Mekanik Turbin', 'Teknisi Senior', '081234567890', '320101xxxxxxxxxx', 'ahmad@example.com', 'password123', '2026-07-01', '2026-07-20'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
