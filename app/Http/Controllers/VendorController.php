<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(10);
        return view('vendor.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kontak'      => 'nullable|string|max:255',
            'alamat'      => 'nullable|string',
        ]);

        Vendor::create($data);

        return back()->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'kontak'      => 'nullable|string|max:255',
            'alamat'      => 'nullable|string',
        ]);

        $vendor->update($data);

        return back()->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return back()->with('success', 'Vendor berhasil dihapus.');
    }
}