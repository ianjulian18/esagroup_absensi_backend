<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\API\ApiAttendanceController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/scan', [\App\Http\Controllers\API\ApiAttendanceController::class, 'scan']);
    Route::get('/attendance/history', [\App\Http\Controllers\API\ApiAttendanceController::class, 'history']);
});