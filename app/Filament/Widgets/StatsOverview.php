<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Attendance;
use App\Models\VisitLog;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    // Mengatur urutan agar widget ini tampil paling atas
    protected static ?int $sort = 1; 

    protected function getStats(): array
    {
        return [
            Stat::make('Total Karyawan', User::where('role', 'karyawan')->count() ?? 0)
                ->description('Jumlah Karyawan Aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Hadir Hari Ini', Attendance::whereDate('date', Carbon::today())->count() ?? 0)
                ->description('Karyawan yang sudah Check-In')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
                
            Stat::make('Open Issues (Visit)', VisitLog::where('status', 'open')->count() ?? 0)
                ->description('Butuh tindakan HRD')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}