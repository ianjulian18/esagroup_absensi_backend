<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\WorkingGroupSchedule;

class ScheduleController extends Controller
{
    public function today(Request $request)
    {
        $user = $request->user();
        
        $todayStr = now()->toDateString();
        $itinerary = \App\Models\Itinerary::with('workingHour')
            ->where('user_id', $user->id)
            ->where('date', $todayStr)
            ->first();

        $scheduleData = null;
        $isFirstVisitLocked = false;
        $stores = [];

        if ($itinerary) {
            $scheduleData = (object) [
                'type' => 'individual_override',
                'working_hour' => $itinerary->workingHour,
                'late_tolerance' => 15,
                'routing_type' => $itinerary->routing_type,
                'stores' => $itinerary->stores ?? [],
                'is_first_visit_locked' => $itinerary->is_first_visit_locked,
            ];
            $stores = $itinerary->stores ?? [];
            $isFirstVisitLocked = $itinerary->is_first_visit_locked;
        } else if ($user->working_group_id) {
            $user->load(['workingGroup.schedules.workingHour', 'workingGroup.defaultWorkingHour']);
            $group = $user->workingGroup;

            if ($group && (!$group->date_applied || $todayStr >= $group->date_applied)) {
                $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                $todayIndo = $days[Carbon::now()->format('l')];

                $schedule = $group->schedules->where('day_of_week', $todayIndo)->first();

                if ($schedule) {
                    $scheduleData = (object) [
                        'type' => 'daily_override',
                        'working_hour' => $schedule->workingHour,
                        'late_tolerance' => $schedule->late_tolerance,
                        'routing_type' => $schedule->routing_type,
                        'stores' => $schedule->stores ?? [],
                        'is_first_visit_locked' => $group->is_first_visit_locked,
                    ];
                    $stores = $schedule->stores ?? [];
                    $isFirstVisitLocked = $group->is_first_visit_locked;
                }
            }
        }

        if (!$scheduleData) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada jadwal shift terstruktur (Bebas).',
                'data' => null
            ], 200);
        }

        $user->load('location');
        $officeLat = $user->location ? (float)$user->location->latitude : -7.2356163;
        $officeLon = $user->location ? (float)$user->location->longitude : 112.73303;

        // Shift location to first store if locked
        if ($isFirstVisitLocked && !empty($stores)) {
            $firstStore = \App\Models\Store::where('name', $stores[0])->first();
            if ($firstStore) {
                $officeLat = (float)$firstStore->latitude;
                $officeLon = (float)$firstStore->longitude;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal hari ini berhasil ditarik.',
            'data' => [
                'group_name' => $user->workingGroup ? $user->workingGroup->name : 'Individual',
                'day' => Carbon::now()->format('l'),
                'shift_name' => $scheduleData->working_hour ? $scheduleData->working_hour->name : 'Shift Bebas',
                'start_time' => $scheduleData->working_hour ? $scheduleData->working_hour->start_time : '08:00:00',
                'end_time' => $scheduleData->working_hour ? $scheduleData->working_hour->end_time : '17:00:00',
                'late_tolerance' => $scheduleData->late_tolerance,
                'routing_type' => $scheduleData->routing_type,
                'stores' => $stores,
                'is_first_visit_locked' => $isFirstVisitLocked,
                'office_latitude' => $officeLat,
                'office_longitude' => $officeLon,
            ]
        ], 200);
    }

    public function future(Request $request)
    {
        $user = $request->user();
        
        if (!$user->working_group_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum dimasukkan ke dalam Grup Kerja.',
            ], 404);
        }

        $user->load(['workingGroup.schedules.workingHour', 'workingGroup.defaultWorkingHour']);
        $group = $user->workingGroup;

        $daysMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $scheduleList = [];
        $today = Carbon::today();

        for ($i = 0; $i < 30; $i++) {
            $currentDate = $today->copy()->addDays($i);
            $dayEnglish = $currentDate->format('l');
            $dayIndo = $daysMap[$dayEnglish];

            $schedule = $group->schedules->where('day_of_week', $dayIndo)->first();

            if ($schedule) {
                $scheduleList[] = [
                    'date' => $currentDate->toDateString(),
                    'day' => $dayIndo,
                    'shift_name' => $schedule->workingHour ? $schedule->workingHour->name : 'Shift Bebas',
                    'start_time' => $schedule->workingHour ? $schedule->workingHour->start_time : '08:00:00',
                    'end_time' => $schedule->workingHour ? $schedule->workingHour->end_time : '17:00:00',
                    'routing_type' => $schedule->routing_type,
                    'stores' => $schedule->stores ?? [],
                    'is_holiday' => false,
                ];
            } else {
                // Fallback to default baseline
                $scheduleList[] = [
                    'date' => $currentDate->toDateString(),
                    'day' => $dayIndo,
                    'shift_name' => $group->defaultWorkingHour ? $group->defaultWorkingHour->name : 'Default',
                    'start_time' => $group->defaultWorkingHour ? $group->defaultWorkingHour->start_time : '08:00:00',
                    'end_time' => $group->defaultWorkingHour ? $group->defaultWorkingHour->end_time : '17:00:00',
                    'routing_type' => 'bebas_visit',
                    'stores' => $group->default_stores ?? [],
                    'is_holiday' => false, // Can be refined later based on holidays table
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal 30 hari kedepan berhasil ditarik.',
            'data' => $scheduleList
        ], 200);
    }
}