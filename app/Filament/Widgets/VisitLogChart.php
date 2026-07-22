<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VisitLog;

class VisitLogChart extends ChartWidget
{
    
    protected ?string $heading = 'Statistik Laporan Visit (Visit Logs)';

    // Untuk $sort tetap menggunakan static
    protected static ?int $sort = 2;
    

    protected function getData(): array
    {
        // Menghitung jumlah masing-masing status dari database
        $open = VisitLog::where('status', 'open')->count();
        $actionTaken = VisitLog::where('status', 'action_taken')->count();
        $completed = VisitLog::where('status', 'completed')->count();
        $overdue = VisitLog::where('status', 'overdue')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Laporan',
                    'data' => [$open, $actionTaken, $completed, $overdue],
                    'backgroundColor' => [
                        '#f43f5e', // Merah untuk Open
                        '#3b82f6', // Biru untuk Action Taken
                        '#10b981', // Hijau untuk Completed
                        '#f59e0b', // Oranye untuk Overdue
                    ],
                ],
            ],
            'labels' => ['Open Issue', 'Action Taken', 'Completed', 'Overdue'],
        ];
    }

    protected function getType(): string
    {
        // Bisa diubah jadi 'pie', 'bar', atau 'line' jika bosan
        return 'doughnut'; 
    }
}