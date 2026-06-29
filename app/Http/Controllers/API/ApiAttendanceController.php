<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\ValidationException;

class ApiAttendanceController extends Controller
{
    // RUMUS PENGHITUNG JARAK (Haversine Formula)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    // 1. FUNGSI LOGIN (SUDAH DINAMIS)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Eager load relasi 'location' agar data lokasi kantor karyawan ikut terambil
        $user = User::with('location')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if ($user->is_resign == 1) { 
            return response()->json(['message' => 'Maaf, Anda sudah tidak bekerja lagi.'], 403); 
        }

        $token = $user->createToken('flutter-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'karyawan',
                // Kirim data lokasi dinamis ke Flutter. Jika belum disetting HRD, pakai fallback default
                'office_latitude' => $user->location ? (float)$user->location->latitude : -7.2356163,
                'office_longitude' => $user->location ? (float)$user->location->longitude : 112.73303,
                'max_radius' => $user->location ? (float)$user->location->radius : 50.0,
                'is_location_locked' => $user->is_location_locked ?? 1,
            ]
        ]);
    }

    // 2. FUNGSI PROSES ABSEN (GEOFENCING SUDAH DINAMIS)
    public function scan(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'photo' => 'required|image|max:2048', 
        ]);

        $user = $request->user();
        
        // Pastikan relasi location terpanggil untuk validasi backend
        $user->load('location');

        // Tarik lokasi dinamis karyawan untuk validasi jarak di backend
        $officeLat = $user->location ? (float)$user->location->latitude : -7.2356163;
        $officeLon = $user->location ? (float)$user->location->longitude : 112.73303;
        $maxRadius = $user->location ? (float)$user->location->radius : 50.0;

        $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLon);

        if ($distance > $maxRadius) {
            return response()->json([
                'message' => 'Anda berada di luar jangkauan kantor! Jarak Anda: ' . round($distance) . ' meter dari batas ' . $maxRadius . ' meter.'
            ], 403);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        $today = now()->toDateString();
        $timeNow = now()->toTimeString();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        // JIKA BELUM ABSEN MASUK
        if (!$attendance) {
            $status = 'hadir';
            if (now()->format('H:i') > '08:00') {
                $status = 'terlambat';
            }

            $newAttendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => $timeNow,
                'status' => $status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'photo_in' => $photoPath,
            ]);

            return response()->json(['message' => 'Berhasil absen masuk!', 'data' => $newAttendance], 201);
        }

        // JIKA SUDAH ABSEN MASUK & BELUM ABSEN PULANG
        if ($attendance && $attendance->clock_out === null) {
            $attendance->update([
                'clock_out' => $timeNow,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'photo_out' => $photoPath,
            ]);

            return response()->json(['message' => 'Berhasil absen pulang! Hati-hati di jalan.', 'data' => $attendance], 200);
        }

        return response()->json(['message' => 'Kamu sudah menyelesaikan absensi untuk hari ini.'], 400);
    }

    // 3. FUNGSI TARIK DATA RIWAYAT ABSENSI
    public function history(Request $request)
    {
        $user = $request->user();
        
        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil riwayat',
            'data' => $history
        ], 200);
    }
}