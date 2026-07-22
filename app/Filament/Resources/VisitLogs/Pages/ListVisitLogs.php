<?php

namespace App\Filament\Resources\VisitLogs\Pages;

use App\Filament\Resources\VisitLogs\VisitLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisitLogs extends ListRecords
{
    protected static string $resource = VisitLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \pxlrbt\FilamentExcel\Actions\Pages\ExportAction::make()
                ->exports([
                    \pxlrbt\FilamentExcel\Exports\ExcelExport::make()
                        ->fromTable()
                        ->withFilename('Laporan_Visit_Log_' . date('Y-m-d'))
                ]),
            CreateAction::make(),
        ];
    }
}
