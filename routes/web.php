<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\GeofenceZoneController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\FaceRecognitionController;
use App\Http\Controllers\Attendance\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('attendance.check-in'));

// Debug route untuk troubleshoot view loading
Route::get('/debug-views', function () {
    $viewPath = base_path('resources/views/admin/staffs/create.blade.php');
    $exists = file_exists($viewPath);
    $readable = is_readable($viewPath);
    $size = $exists ? filesize($viewPath) : 0;
    
    return response("Debug Info:\n
File path: {$viewPath}
Exists: " . ($exists ? 'YES' : 'NO') . "
Readable: " . ($readable ? 'YES' : 'NO') . "
Size: {$size} bytes
View paths: " . implode(', ', config('view.paths')) . "
", 200, ['Content-Type' => 'text/plain']);
});

// Test route to load the view directly
Route::get('/test-view-load', function () {
    return view('admin.staffs.create');
});


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Employee Login & Registration Routes
Route::get('/employee/login', [EmployeeLoginController::class, 'showLoginForm'])->name('employee.login');
Route::post('/employee/login', [EmployeeLoginController::class, 'login']);
Route::get('/employee/register', [EmployeeLoginController::class, 'showRegisterForm'])->name('employee.register');
Route::post('/employee/register', [EmployeeLoginController::class, 'register']);
Route::post('/employee/logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');

Route::prefix('attendance')->name('attendance.')->middleware(['auth.employee'])->group(function () {
    Route::get('/check-in', [AttendanceController::class, 'showCheckIn'])->name('check-in');
    Route::get('/qr-fallback', [AttendanceController::class, 'showQrFallback'])->name('qr-fallback');
    Route::get('/history/{staff_id}', [AttendanceController::class, 'showHistory'])->name('history');
    Route::get('/permit', [AttendanceController::class, 'showPermitForm'])->name('permit');
    Route::post('/permit', [AttendanceController::class, 'storePermitRequest'])->name('permit.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('staffs/{staff}/attendance', [StaffController::class, 'storeManualAttendance'])->name('staffs.attendance.store');
    
    // Ekspor Data Evakuasi Darurat K3
    Route::get('/evacuation/export/csv', [StaffController::class, 'exportEvacuationCsv'])->name('evacuation.export.csv');
    Route::get('/evacuation/export/json', [StaffController::class, 'exportEvacuationJson'])->name('evacuation.export.json');
    Route::get('staffs/download-template', [StaffController::class, 'downloadTemplate'])->name('staffs.download-template');
    Route::resource('staffs', StaffController::class);
    Route::resource('geofences', GeofenceZoneController::class);

    // Profil admin
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Laporan presensi
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// API Routes merged here for convenience (normally in api.php)
Route::prefix('api/attendance')->group(function () {
    Route::post('/process', [AttendanceController::class, 'processHybridAttendance']);
});

Route::prefix('api/face')->group(function () {
    Route::get('/descriptors', [FaceRecognitionController::class, 'getFaceDescriptors']);
    Route::post('/match', [FaceRecognitionController::class, 'matchFace']);
    Route::post('/register', [FaceRecognitionController::class, 'registerFace']);
});

Route::prefix('api/qr')->group(function () {
    Route::post('/generate', [QrCodeController::class, 'generateQr']);
    Route::post('/validate', [QrCodeController::class, 'validateQr']);
});

Route::prefix('api/dashboard')->group(function () {
    Route::get('/realtime', [DashboardController::class, 'getRealtimeData']);
    Route::get('/activity-log', [DashboardController::class, 'getActivityLog']);
    Route::get('/hourly-population', [DashboardController::class, 'getHourlyPopulation']);
});

Route::post('admin/staffs/import', [StaffController::class, 'import'])->name('admin.staffs.import');