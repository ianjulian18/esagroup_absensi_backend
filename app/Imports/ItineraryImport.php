<?php

namespace App\Imports;

use App\Models\Itinerary;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItineraryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Cari user berdasarkan email
        $user = User::where('email', $row['email'])->first();

        if (!$user) {
            return null; // Abaikan jika user tidak ditemukan
        }

        // Parse stores dari string dipisah koma (contoh: "Toko A, Toko B")
        $storesArray = [];
        if (!empty($row['stores'])) {
            $storesArray = array_map('trim', explode(',', $row['stores']));
        }

        return Itinerary::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $row['tanggal'], // format YYYY-MM-DD
            ],
            [
                'working_hour_id' => $row['shift_id'],
                'routing_type' => $row['routing_type'] ?? 'bebas_visit',
                'stores' => $storesArray,
                'is_first_visit_locked' => strtolower($row['first_visit_locked']) === 'ya' || $row['first_visit_locked'] == 1,
            ]
        );
    }
}
