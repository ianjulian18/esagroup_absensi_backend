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
    // Helper untuk mapping hari Bahasa Inggris ke Bahasa Indonesia
    private function getIndonesianDay()
    {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        return $days[now()->format('l')] ?? 'Senin';
    }

    // Helper untuk mendapatkan Jadwal Roster Hari Ini
    private function getTodaySchedule($user)
    {
        // 1. Cek Itinerary Spesifik (Individual Override)
        $todayStr = now()->toDateString();
        $itinerary = \App\Models\Itinerary::with('workingHour')
            ->where('user_id', $user->id)
            ->where('date', $todayStr)
            ->first();

        if ($itinerary) {
            return (object) [
                'type' => 'individual_override',
                'working_hour' => $itinerary->workingHour,
                'late_tolerance' => 15, // Default for now, can be added to Itinerary if needed
                'routing_type' => $itinerary->routing_type,
                'stores' => $itinerary->stores ?? [],
                'is_first_visit_locked' => $itinerary->is_first_visit_locked, // Mengikuti settingan dari Itinerary spesifik
            ];
        }

        // 2. Fallback ke Aturan Working Group
        if (!$user->working_group_id) {
            return null; // Fallback ke logika lama jika user tidak punya grup
        }

        $user->load(['workingGroup.schedules.workingHour', 'workingGroup.defaultWorkingHour']);
        $group = $user->workingGroup;

        if (!$group) return null;

        // Cek apakah tanggal mulai berlaku (date_applied) sudah lewat atau sama dengan hari ini
        $todayDate = now()->toDateString();
        if ($group->date_applied && $todayDate < $group->date_applied) {
            return null; // Aturan grup belum aktif, fallback ke default/kosong
        }

        $today = $this->getIndonesianDay();
        
        // Cari jadwal khusus untuk hari ini (Days Applied)
        $schedule = $group->schedules->where('day_of_week', $today)->first();

        if ($schedule) {
            return (object) [
                'type' => 'daily_override',
                'working_hour' => $schedule->workingHour,
                'late_tolerance' => $schedule->late_tolerance,
                'routing_type' => $schedule->routing_type,
                'stores' => $schedule->stores ?? [], // array nama toko
                'is_first_visit_locked' => $group->is_first_visit_locked, // Ambil dari grup
            ];
        }

        // Fallback ke Aturan General (Baseline)
        return (object) [
            'type' => 'default_baseline',
            'working_hour' => $group->defaultWorkingHour,
            'late_tolerance' => $group->default_late_tolerance,
            'routing_type' => 'bebas_visit', // default
            'stores' => $group->default_stores ?? [],
            'is_first_visit_locked' => $group->is_first_visit_locked,
        ];
    }

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
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::with(['location', 'entity', 'principals', 'roles'])
            ->where('email', $request->email)
            ->orWhere('nik', $request->email)
            ->orWhere('nip', $request->email)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email/NIK/NIP atau password salah.'], 401);
        }

        if ($user->is_resign == 1) { 
            return response()->json(['message' => 'Maaf, Anda sudah tidak bekerja lagi.'], 403); 
        }

        $incomingDeviceId = $request->input('device_id');
        if ($incomingDeviceId) {
            if (empty($user->device_id)) {
                $user->update(['device_id' => $incomingDeviceId]);
            } else if ($user->device_id !== $incomingDeviceId) {
                return response()->json([
                    'message' => 'Akun ini sudah terikat dengan perangkat lain. Hubungi Admin jika Anda ganti HP.'
                ], 403);
            }
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
                'role' => $user->roles->first()->name ?? $user->role ?? 'karyawan',
                'entity_id' => $user->entity_id,
                'entity_name' => $user->entity ? $user->entity->name : 'Unknown Entity',
                'principals' => $user->principals->map(function ($principal) {
                    return [
                        'id' => $principal->id,
                        'name' => $principal->name,
                    ];
                }),
                'office_latitude' => $user->location ? (float)$user->location->latitude : -7.2356163,
                'office_longitude' => $user->location ? (float)$user->location->longitude : 112.73303,
                'max_radius' => $user->location ? (float)$user->location->radius : 50.0,
                'is_location_locked' => $user->is_location_locked ?? 1,
            ]
        ]);
    }

    // 2. FUNGSI PROSES ABSEN (GEOFENCING DINAMIS + ROSTER)
    public function scan(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'photo' => 'required|image|max:2048', 
        ]);

        $user = $request->user();
        $user->load('location');

        $schedule = $this->getTodaySchedule($user);
        
        $officeLat = $user->location ? (float)$user->location->latitude : -7.2356163;
        $officeLon = $user->location ? (float)$user->location->longitude : 112.73303;
        $maxRadius = $user->location ? (float)$user->location->radius : 50.0;

        // Logika First Visit Lock (Jika Wajib Absen di Toko Pertama)
        if ($schedule && $schedule->is_first_visit_locked && !empty($schedule->stores)) {
            $firstStoreName = $schedule->stores[0];
            $firstStore = \App\Models\Store::where('name', $firstStoreName)->first();
            if ($firstStore) {
                $officeLat = (float)$firstStore->latitude;
                $officeLon = (float)$firstStore->longitude;
            }
        }

        $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLon);

        if ($distance > $maxRadius) {
            return response()->json([
                'message' => 'Anda berada di luar jangkauan radius (' . round($distance) . 'm) dari batas ' . $maxRadius . 'm.'
            ], 403);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        $today = now()->toDateString();
        $timeNow = now()->toTimeString();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance) {
            // Tentukan status Terlambat berdasarkan Roster
            $status = 'hadir';
            $startTime = '08:00:00';
            $lateTolerance = 15;

            if ($schedule && $schedule->working_hour) {
                $startTime = $schedule->working_hour->start_time; // Misal: 08:00:00
                $lateTolerance = $schedule->late_tolerance;
            }

            // Gabungkan tanggal hari ini dengan waktu mulai shift
            $shiftStartTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $startTime);
            $limitTime = $shiftStartTime->addMinutes($lateTolerance);

            if (now()->isAfter($limitTime)) {
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

    // --- FUNGSI BARU: VISIT IN (DENGAN LOGIKA ROSTER) ---
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

        // LOGIKA ROSTER: Validasi Routing Aktif / Bebas Visit
        $schedule = $this->getTodaySchedule($user);
        if ($schedule && !empty($schedule->stores)) {
            $allowedStores = $schedule->stores;
            $requestedStore = $request->location_name;

            // Jika Routing Aktif, cek urutan
            if ($schedule->routing_type === 'routing_aktif') {
                // Cari toko mana yang harus dikunjungi selanjutnya
                // Berapa visit yang sudah completed hari ini?
                $completedVisitsCount = $attendance->visits()->where('status', 'completed')->count();
                
                if ($completedVisitsCount < count($allowedStores)) {
                    $expectedStore = $allowedStores[$completedVisitsCount];
                    if (strcasecmp($requestedStore, $expectedStore) !== 0) {
                        return response()->json([
                            'message' => 'Routing Aktif (Terkunci): Tujuan Anda selanjutnya seharusnya adalah ' . $expectedStore
                        ], 403);
                    }
                } else {
                    // Jika sudah visit semua toko di jadwal, mungkin boleh visit bebas?
                    // Untuk saat ini, asumsikan ditolak jika lewat batas
                     return response()->json([
                        'message' => 'Routing Aktif (Terkunci): Anda sudah menyelesaikan semua rute visit wajib hari ini.'
                    ], 403);
                }
            } else {
                // Jika Bebas Visit, pastikan minimal toko ada di daftar (opsional)
                // Kita izinkan jika memang bebas visit, tapi jika ingin dikunci ke daftar:
                // if (!in_array($requestedStore, $allowedStores)) {
                //    return response()->json(['message' => 'Toko tidak ada dalam jadwal Anda hari ini.'], 403);
                // }
            }
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

    // --- FUNGSI BARU: VISIT OUT (DENGAN HITUNG DURASI) ---
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
        
        // Hitung durasi visit dalam menit
        $visitIn = \Carbon\Carbon::parse($visit->visit_in);
        $visitOut = \Carbon\Carbon::parse($timeNow);
        $durationMinutes = $visitIn->diffInMinutes($visitOut);

        $visit->update([
            'visit_out' => $timeNow,
            'latitude_out' => $request->latitude,
            'longitude_out' => $request->longitude,
            'photo_out' => $photoPath,
            'status' => 'completed',
            'duration_minutes' => $durationMinutes // Simpan durasi jika kolom ada di DB (asumsi ditambahkan nanti jika belum ada)
        ]);

        return response()->json([
            'message' => 'Visit Out berhasil. Durasi visit: ' . $durationMinutes . ' menit.', 
            'data' => $visit
        ], 200);
    }
}