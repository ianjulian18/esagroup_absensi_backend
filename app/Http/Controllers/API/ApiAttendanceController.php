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
        $earthRadius = 6371000; // Radius bumi dalam satuan meter

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

    // 1. FUNGSI LOGIN (Bisa pakai Email, NIK, atau NIP)
    public function login(Request $request)
    {
        // Validasi diubah: email diubah menjadi string (bukan format @mail) agar NIK/NIP bisa masuk
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        // Cerdas mencari user: Cocokkan dengan Email, ATAU NIK, ATAU NIP
        $user = User::with('location')
            ->where('email', $request->email)
            ->orWhere('nik', $request->email)
            ->orWhere('nip', $request->email)
            ->first();

        // Cek kecocokan password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email/NIK/NIP atau password salah.'], 401);
        }

        // Cek proteksi karyawan resign
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
                'nik' => $user->nik,
                'nip' => $user->nip,
                'role' => $user->role ?? 'karyawan',
                'office_latitude' => $user->location ? (float)$user->location->latitude : -7.2356163,
                'office_longitude' => $user->location ? (float)$user->location->longitude : 112.73303,
                'max_radius' => $user->location ? (float)$user->location->radius : 50.0,
                'is_location_locked' => $user->is_location_locked ?? 1,
            ]
        ]);
    }

    // 2. FUNGSI PROSES ABSEN (GEOFENCING DINAMIS)
    public function scan(Request $request)
    {
        // 1. Validasi Input (Wajib kirim kordinat dan file foto)
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

        // 2. CEK RADAR JARAK
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLon);

        if ($distance > $maxRadius) {
            return response()->json([
                'message' => 'Anda berada di luar jangkauan kantor! Jarak Anda: ' . round($distance) . ' meter dari batas ' . $maxRadius . ' meter.'
            ], 403);
        }

        // 3. PROSES SIMPAN FOTO KE SERVER
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        // 4. PROSES SIMPAN ABSENSI KE DATABASE
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
                'photo_in' => $photoPath, // Simpan foto masuk
            ]);

            return response()->json(['message' => 'Berhasil absen masuk!', 'data' => $newAttendance], 201);
        }

        // JIKA SUDAH ABSEN MASUK & BELUM ABSEN PULANG
        if ($attendance && $attendance->clock_out === null) {
            $attendance->update([
                'clock_out' => $timeNow,
                'latitude' => $request->latitude, // Perbarui kordinat saat pulang
                'longitude' => $request->longitude,
                'photo_out' => $photoPath, // Simpan foto pulang
            ]);

            return response()->json(['message' => 'Berhasil absen pulang! Hati-hati di jalan.', 'data' => $attendance], 200);
        }

        return response()->json(['message' => 'Kamu sudah menyelesaikan absensi untuk hari ini.'], 400);
    }

    // 3. FUNGSI TARIK DATA RIWAYAT ABSENSI
    public function history(Request $request)
    {
        $user = $request->user();
        
        // Ambil riwayat absen karyawan yang login (30 hari terakhir agar database tidak berat)
        $history = Attendance::with(['visits','user.location'])
            -> where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil riwayat',
            'data' => $history
        ], 200);
    }

    // --- FUNGSI BARU: VISIT IN ---
    public function visitIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'photo' => 'required|image|max:2048',
            'location_name' => 'required|string',
        ]);

        $user = $request->user();
        $today = now()->toDateString();
        $timeNow = now()->toTimeString();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        // Cek aturan alur absen
        if (!$attendance) {
            return response()->json(['message' => 'Anda harus Check-In terlebih dahulu sebelum melakukan Visit.'], 403);
        }
        if ($attendance->clock_out !== null) {
            return response()->json(['message' => 'Anda sudah Check-Out. Sesi visit hari ini telah ditutup.'], 403);
        }

        $activeVisit = $attendance->visits()->where('status', 'in_progress')->first();
        if ($activeVisit) {
            return response()->json(['message' => 'Selesaikan dulu visit di: ' . $activeVisit->location_name], 403);
        }

        $photoPath = $request->file('photo')->store('visits', 'public');

        $visit = $attendance->visits()->create([
            'visit_type' => 'store',
            'location_name' => $request->location_name,
            'visit_in' => $timeNow,
            'latitude_in' => $request->latitude,
            'longitude_in' => $request->longitude,
            'photo_in' => $photoPath,
            'status' => 'in_progress'
        ]);

        return response()->json(['message' => 'Berhasil Visit In di ' . $request->location_name, 'data' => $visit], 201);
    }

    // --- FUNGSI BARU: VISIT OUT ---
    public function visitOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'photo' => 'required|image|max:2048',
            'visit_id' => 'required|exists:visits,id',
        ]);

        $timeNow = now()->toTimeString();
        $visit = \App\Models\Visit::find($request->visit_id);

        if ($visit->status === 'completed') {
            return response()->json(['message' => 'Visit ini sudah diselesaikan sebelumnya.'], 400);
        }

        $photoPath = $request->file('photo')->store('visits', 'public');

        $visit->update([
            'visit_out' => $timeNow,
            'latitude_out' => $request->latitude,
            'longitude_out' => $request->longitude,
            'photo_out' => $photoPath,
            'status' => 'completed'
        ]);

        return response()->json(['message' => 'Visit Out berhasil. Durasi visit tercatat.', 'data' => $visit], 200);
    }
}