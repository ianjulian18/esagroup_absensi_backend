<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiAttendanceController;
use App\Http\Controllers\API\ApiLeaveController;
use App\Http\Controllers\API\ApiExtraHourController;
use App\Http\Controllers\API\ApiBapController;
use App\Http\Controllers\API\ApiPayslipController;
use App\Http\Controllers\API\ApiVisitLogController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\API\ApiStoreController;

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
    // --- RUTE HRIS (FASE 2) ---
    Route::post('/leave/request', [ApiLeaveController::class, 'store']);
    // --- RUTE LEMBUR ---
    Route::post('/extra-hour/request', [ApiExtraHourController::class, 'store']);
    // --- RUTE BAP (MANUAL ABSEN) ---
    Route::post('/bap/request', [ApiBapController::class, 'store']);
    // --- RUTE PAYSLIP (SLIP GAJI) ---
    Route::get('/payslips', [ApiPayslipController::class, 'index']);
    // --- RUTE visit log  ---
    Route::post('/visit-log/submit', [ApiVisitLogController::class, 'store']);
    // RUTE BARU UNTUK MENARIK JADWAL ROSTER
    Route::get('/my-schedule', [ScheduleController::class, 'today']);
    Route::get('/my-schedule/future', [ScheduleController::class, 'future']);
    
    // --- RUTE PENGAJUAN STORE BARU ---
    Route::post('/stores/request', [ApiStoreController::class, 'store']);
});