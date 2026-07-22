<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bap;
use Illuminate\Http\Request;

class ApiBapController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:masuk,pulang',
            'time' => 'required|date_format:H:i',
            'reason' => 'required|string',
            'proof' => 'required|image|max:2048', // Wajib ada foto bukti untuk BAP
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('baps', 'public');
        }

        $bap = Bap::create([
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'type' => $request->type,
            'time' => $request->time,
            'reason' => $request->reason,
            'proof_path' => $proofPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan BAP berhasil dikirim!',
            'data' => $bap
        ], 201);
    }
}