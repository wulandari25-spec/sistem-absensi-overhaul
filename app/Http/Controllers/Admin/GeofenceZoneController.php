<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeofenceZone;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class GeofenceZoneController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if ($request->user() && $request->user()->isK3() && in_array($request->route()->getActionMethod(), ['create', 'store', 'edit', 'update', 'destroy'])) {
                    abort(403, 'Akses ditolak: Petugas K3 hanya diizinkan memantau data.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Tampilkan daftar zona geofence.
     */
    public function index()
    {
        $zones = GeofenceZone::latest()->paginate(10);
        return view('admin.geofences.index', compact('zones'));
    }

    /**
     * Tampilkan form pembuatan zona baru.
     */
    public function create()
    {
        return view('admin.geofences.create');
    }

    /**
     * Simpan zona baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_name' => ['required', 'string', 'max:255'],
            'zone_code' => ['required', 'string', 'max:50', 'unique:geofence_zones,zone_code'],
            'center_lat' => ['required', 'numeric', 'between:-90,90'],
            'center_lng' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:5', 'max:10000'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'zone_name.required' => 'Nama zona wajib diisi.',
            'zone_code.required' => 'Kode zona wajib diisi.',
            'zone_code.unique' => 'Kode zona sudah terdaftar.',
            'center_lat.required' => 'Koordinat Latitude wajib diisi.',
            'center_lng.required' => 'Koordinat Longitude wajib diisi.',
            'radius_meters.required' => 'Radius wajib diisi.',
            'radius_meters.min' => 'Radius minimal adalah 5 meter.',
        ]);

        // Default value for checkbox
        $validated['is_active'] = $request->has('is_active');

        GeofenceZone::create($validated);

        return redirect()->route('admin.geofences.index')->with('success', 'Zona geofence berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit zona.
     */
    public function edit(GeofenceZone $geofence)
    {
        return view('admin.geofences.edit', compact('geofence'));
    }

    /**
     * Perbarui data zona di database.
     */
    public function update(Request $request, GeofenceZone $geofence)
    {
        $validated = $request->validate([
            'zone_name' => ['required', 'string', 'max:255'],
            'zone_code' => ['required', 'string', 'max:50', 'unique:geofence_zones,zone_code,' . $geofence->id],
            'center_lat' => ['required', 'numeric', 'between:-90,90'],
            'center_lng' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:5', 'max:10000'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'zone_name.required' => 'Nama zona wajib diisi.',
            'zone_code.required' => 'Kode zona wajib diisi.',
            'zone_code.unique' => 'Kode zona sudah terdaftar.',
            'center_lat.required' => 'Koordinat Latitude wajib diisi.',
            'center_lng.required' => 'Koordinat Longitude wajib diisi.',
            'radius_meters.required' => 'Radius wajib diisi.',
            'radius_meters.min' => 'Radius minimal adalah 5 meter.',
        ]);

        // Default value for checkbox
        $validated['is_active'] = $request->has('is_active');

        $geofence->update($validated);

        return redirect()->route('admin.geofences.index')->with('success', 'Zona geofence berhasil diperbarui.');
    }

    /**
     * Hapus zona dari database.
     */
    public function destroy(GeofenceZone $geofence)
    {
        // Cegah penghapusan jika masih ada data absensi yang terikat
        if ($geofence->attendances()->count() > 0) {
            // Sebagai gantinya, nonaktifkan saja
            $geofence->update(['is_active' => false]);
            return redirect()->route('admin.geofences.index')->with('success', 'Zona dinonaktifkan karena masih terikat dengan data riwayat absensi.');
        }

        $geofence->delete();

        return redirect()->route('admin.geofences.index')->with('success', 'Zona geofence berhasil dihapus.');
    }
}
