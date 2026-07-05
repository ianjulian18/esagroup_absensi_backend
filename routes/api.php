<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiAttendanceController;

Route::post('/login', [\App\Http\Controllers\API\ApiAttendanceController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/scan', [\App\Http\Controllers\API\ApiAttendanceController::class, 'scan']);
    Route::get('/attendance/history', [\App\Http\Controllers\API\ApiAttendanceController::class, 'history']);
    // Rute Absensi yang sudah ada
    Route::post('/attendance/scan', [ApiAttendanceController::class, 'scan']);
    Route::get('/attendance/history', [ApiAttendanceController::class, 'history']);
    
    // --- RUTE BARU UNTUK VISIT ---
    Route::post('/visit/in', [ApiAttendanceController::class, 'visitIn']);
    Route::post('/visit/out', [ApiAttendanceController::class, 'visitOut']);
});