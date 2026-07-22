<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class ApiStoreController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'address' => 'required|string',
            'sub_area_id' => 'nullable|exists:sub_areas,id',
            'channel_id' => 'nullable|exists:channels,id',
        ]);

        $store = Store::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'sub_area_id' => $request->sub_area_id,
            'channel_id' => $request->channel_id,
            'status' => 'pending', // Menunggu persetujuan admin
        ]);

        return response()->json([
            'message' => 'Pengajuan lokasi baru berhasil dikirim dan menunggu persetujuan admin.',
            'data' => $store
        ], 201);
    }
}
