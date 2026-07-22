<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use Illuminate\Http\Request;

class ApiPayslipController extends Controller
{
    // Mengambil daftar slip gaji milik user yang sedang login
    public function index(Request $request)
    {
        $payslips = Payslip::where('user_id', $request->user()->id)
            ->where('status', 'published') // Karyawan hanya bisa melihat yang statusnya sudah 'published'
            ->orderBy('period', 'desc')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil data slip gaji',
            'data' => $payslips
        ], 200);
    }
}