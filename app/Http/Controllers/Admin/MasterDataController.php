<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\MasterInstitution;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
 
class MasterDataController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = $request->user();
                if ($user) {
                    if (($user->isK3() || $user->isSecurity()) && in_array($request->route()->getActionMethod(), [
                        'storeInstitution', 'updateInstitution', 'destroyInstitution',
                        'storeDepartment', 'updateDepartment', 'destroyDepartment',
                        'storePosition', 'updatePosition', 'destroyPosition'
                    ])) {
                        abort(403, 'Akses ditolak: Hanya Administrator yang diizinkan mengelola data master.');
                    }
                }
                return $next($request);
            }),
        ];
    }
 
    public function index()
    {
        $institutions = MasterInstitution::orderBy('name')->get();
        $departments = MasterDepartment::orderBy('name')->get();
        $positions = MasterPosition::orderBy('name')->get();
 
        return view('admin.master_data.index', compact('institutions', 'departments', 'positions'));
    }
 
    // Institution CRUD
    public function storeInstitution(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:master_institutions,name',
        ], [
            'name.unique' => 'Nama instansi sudah terdaftar.',
            'name.required' => 'Nama instansi wajib diisi.',
        ]);
 
        $item = MasterInstitution::create($data);
 
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Instansi berhasil ditambahkan.',
                'data' => $item
            ]);
        }
 
        return redirect()->route('admin.master-data.index')->with('success', 'Instansi berhasil ditambahkan.');
    }
 
    public function updateInstitution(Request $request, $id)
    {
        $institution = MasterInstitution::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:master_institutions,name,' . $id,
        ], [
            'name.unique' => 'Nama instansi sudah terdaftar.',
            'name.required' => 'Nama instansi wajib diisi.',
        ]);
 
        $institution->update($data);
 
        return redirect()->route('admin.master-data.index')->with('success', 'Instansi berhasil diperbarui.');
    }
 
    public function destroyInstitution($id)
    {
        $institution = MasterInstitution::findOrFail($id);
        $institution->delete();
 
        return redirect()->route('admin.master-data.index')->with('success', 'Instansi berhasil dihapus.');
    }
 
    // Department CRUD
    public function storeDepartment(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:master_departments,name',
        ], [
            'name.unique' => 'Nama departemen sudah terdaftar.',
            'name.required' => 'Nama departemen wajib diisi.',
        ]);
 
        $item = MasterDepartment::create($data);
 
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil ditambahkan.',
                'data' => $item
            ]);
        }
 
        return redirect()->route('admin.master-data.index')->with('success', 'Departemen berhasil ditambahkan.');
    }
 
    public function updateDepartment(Request $request, $id)
    {
        $department = MasterDepartment::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:master_departments,name,' . $id,
        ], [
            'name.unique' => 'Nama departemen sudah terdaftar.',
            'name.required' => 'Nama departemen wajib diisi.',
        ]);
 
        $department->update($data);
 
        return redirect()->route('admin.master-data.index')->with('success', 'Departemen berhasil diperbarui.');
    }
 
    public function destroyDepartment($id)
    {
        $department = MasterDepartment::findOrFail($id);
        $department->delete();
 
        return redirect()->route('admin.master-data.index')->with('success', 'Departemen berhasil dihapus.');
    }
 
    // Position CRUD
    public function storePosition(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:master_positions,name',
        ], [
            'name.unique' => 'Nama posisi jabatan sudah terdaftar.',
            'name.required' => 'Nama posisi jabatan wajib diisi.',
        ]);
 
        $item = MasterPosition::create($data);
 
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Posisi jabatan berhasil ditambahkan.',
                'data' => $item
            ]);
        }
 
        return redirect()->route('admin.master-data.index')->with('success', 'Posisi jabatan berhasil ditambahkan.');
    }
 
    public function updatePosition(Request $request, $id)
    {
        $position = MasterPosition::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:master_positions,name,' . $id,
        ], [
            'name.unique' => 'Nama posisi jabatan sudah terdaftar.',
            'name.required' => 'Nama posisi jabatan wajib diisi.',
        ]);
 
        $position->update($data);
 
        return redirect()->route('admin.master-data.index')->with('success', 'Posisi jabatan berhasil diperbarui.');
    }
 
    public function destroyPosition($id)
    {
        $position = MasterPosition::findOrFail($id);
        $position->delete();
 
        return redirect()->route('admin.master-data.index')->with('success', 'Posisi jabatan berhasil dihapus.');
    }
}
