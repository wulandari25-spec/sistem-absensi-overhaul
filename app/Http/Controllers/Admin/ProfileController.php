<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil admin.
     */
    public function show()
    {
        $admin = Auth::user();

        // Ganti dengan query aktivitas log Anda jika sudah ada tabelnya,
        // misalnya model ActivityLog::where('user_id', $admin->id)->latest()->take(5)->get();
        $activities = collect();

        return view('admin.profile.index', [
            'admin'      => $admin,
            'activities' => $activities,
            'lastLogin'  => optional($admin->last_login_at ?? null)->format('H.i'),
            'loginCount' => $admin->login_count ?? null,
        ]);
    }

    /**
     * Update informasi akun (nama, email, telepon, unit).
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'unit'  => ['nullable', 'string', 'max:255'],
        ]);

        $admin->update($validated);

        return back()->with('status', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Update kata sandi admin.
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'current_password'     => ['required'],
            'password'             => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Update foto profil admin.
     */
    public function updatePhoto(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Hapus foto lama jika ada
        if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
            Storage::disk('public')->delete($admin->photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');

        $admin->update(['photo' => $path]);

        return back()->with('status', 'Foto profil berhasil diperbarui.');
    }
}