<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\FaceRecognitionController;
use App\Http\Controllers\Attendance\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::prefix('attendance')->group(function () {
    Route::post('/process', [AttendanceController::class, 'processHybridAttendance']);
});

Route::prefix('face')->group(function () {
    Route::get('/descriptors', [FaceRecognitionController::class, 'getFaceDescriptors']);
    Route::post('/match', [FaceRecognitionController::class, 'matchFace']);
    Route::post('/register', [FaceRecognitionController::class, 'registerFace']);
});

Route::prefix('qr')->group(function () {
    Route::post('/generate', [QrCodeController::class, 'generateQr']);
    Route::post('/validate', [QrCodeController::class, 'validateQr']);
});

Route::prefix('dashboard')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/realtime', [DashboardController::class, 'getRealtimeData']);
    Route::get('/activity-log', [DashboardController::class, 'getActivityLog']);
    Route::get('/hourly-population', [DashboardController::class, 'getHourlyPopulation']);
});
