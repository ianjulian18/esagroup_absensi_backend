<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VisitLog;
use Illuminate\Http\Request;

class ApiVisitLogController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk dari Flutter
        $request->validate([
            'store_name' => 'required|string',
            'issue'      => 'required|string',
            'action'     => 'required|string',
            'target'     => 'required|string',
            'actual'     => 'required|string',
            'deadline'   => 'required|date',
            'notes'      => 'nullable|string',
        ]);

        // 2. Simpan ke database
        $log = VisitLog::create([
            'user_id'    => $request->user()->id,
            'store_name' => $request->store_name,
            'issue'      => $request->issue,
            'action'     => $request->action,
            'target'     => $request->target,
            'actual'     => $request->actual,
            'deadline'   => $request->deadline,
            'notes'      => $request->notes,
            'status'     => 'open', // Status awal saat baru dikirim
        ]);

        // 3. Balas ke Flutter
        return response()->json([
            'message' => 'Laporan Visit Log berhasil dikirim!',
            'data'    => $log
        ], 201);
    }
}