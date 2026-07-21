<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OutsourcingStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeLoginController extends Controller
{
    /**
     * Tampilkan form login karyawan.
     */
    public function showLoginForm()
    {
        if (session()->has('logged_in_staff_id')) {
            return redirect()->route('attendance.check-in');
        }
        return view('auth.employee-login');
    }

    /**
     * Proses autentikasi karyawan.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'staff_code' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'staff_code.required' => 'Kode Staf wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $staff = OutsourcingStaff::where('staff_code', $credentials['staff_code'])
            ->registered()
            ->first();

        if (!$staff || !$staff->password || !Hash::check($credentials['password'], $staff->password)) {
            return back()->withErrors([
                'staff_code' => 'Kode Staf atau kata sandi salah, atau akun belum terdaftar.',
            ])->onlyInput('staff_code');
        }

        // Simpan data login karyawan ke session
        session()->put('logged_in_staff_id', $staff->id);
        session()->put('logged_in_staff_name', $staff->name);
        session()->put('logged_in_staff_code', $staff->staff_code);

        return redirect()->route('attendance.check-in');
    }

    /**
     * Tampilkan form registrasi akun karyawan.
     */
    public function showRegisterForm()
    {
        if (session()->has('logged_in_staff_id')) {
            return redirect()->route('attendance.check-in');
        }
        return view('auth.employee-register');
    }

    /**
     * Proses registrasi pembuatan akun karyawan baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'staff_code' => ['required', 'string'],
            'id_number' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'staff_code.required' => 'Kode Staf wajib diisi.',
            'id_number.required' => 'Nomor Identitas (NIK) wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $staff = OutsourcingStaff::where('staff_code', $validated['staff_code'])
            ->where('id_number', $validated['id_number'])
            ->registered()
            ->first();

        if (!$staff) {
            return back()->withErrors([
                'staff_code' => 'Kombinasi Kode Staf dan NIK tidak ditemukan di sistem.',
            ])->onlyInput('staff_code', 'id_number');
        }

        if ($staff->password) {
            return back()->withErrors([
                'staff_code' => 'Akun untuk Kode Staf ini sudah terdaftar. Silakan login.',
            ])->onlyInput('staff_code');
        }

        // Update password karyawan
        $staff->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employee.login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    /**
     * Proses logout karyawan.
     */
    public function logout()
    {
        session()->forget(['logged_in_staff_id', 'logged_in_staff_name', 'logged_in_staff_code']);
        return redirect()->route('employee.login');
    }
}
