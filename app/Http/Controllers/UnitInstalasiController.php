<?php
// app/Http/Controllers/UnitInstalasiController.php

namespace App\Http\Controllers;

use App\Models\UnitInstalasi;
use Illuminate\Http\Request;

class UnitInstalasiController extends Controller
{
    public function index()
    {
        $units = UnitInstalasi::latest()->get();
        return view('unit.index', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_unit'    => 'required|string|max:255',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'radius_meter' => 'required|integer|min:10',
        ]);

        UnitInstalasi::create($data);

        return back()->with('success', 'Unit instalasi berhasil ditambahkan.');
    }
}