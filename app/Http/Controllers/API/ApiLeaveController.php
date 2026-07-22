<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ApiLeaveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cuti,izin,sakit,shift_swap,extra_off,store_closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'document' => 'nullable|image|max:2048', // Foto opsional (wajib jika sakit biasanya)
        ]);

        $user = $request->user();
        $documentPath = null;

        // Proses simpan file foto jika ada
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('leaves', 'public');
        }

        $leave = Leave::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'document_path' => $documentPath,
            'status' => 'pending', // Status otomatis pending untuk menunggu persetujuan HRD
        ]);

        return response()->json([
            'message' => 'Pengajuan ' . $request->type . ' berhasil dikirim!',
            'data' => $leave
        ], 201);
    }
}
