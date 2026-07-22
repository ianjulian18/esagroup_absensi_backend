<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ExtraHour;
use Illuminate\Http\Request;

class ApiExtraHourController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'reason' => 'required|string',
        ]);
        $isCrossDay = false;
        if (strtotime($request->end_time) < strtotime($request->start_time)) {
            $isCrossDay = true;
        }

        $extraHour = ExtraHour::create([
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'is_cross_day' => $isCrossDay,
            'status' => 'pending', // Status awal otomatis pending
        ]);

        return response()->json([
            'message' => 'Pengajuan lembur berhasil dikirim!',
            'data' => $extraHour
        ], 201);
    }
}