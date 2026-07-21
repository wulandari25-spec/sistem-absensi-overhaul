<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateEmployee
{
    /**
     * Pastikan karyawan sudah login sebelum mengakses halaman presensi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('logged_in_staff_id')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda telah habis. Silakan login kembali.',
                ], 401);
            }
            return redirect()->route('employee.login');
        }

        return $next($request);
    }
}
