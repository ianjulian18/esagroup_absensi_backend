<?php

namespace App\Filament\Resources\VisitLogs\Pages;

use App\Filament\Resources\VisitLogs\VisitLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisitLog extends CreateRecord
{
    protected static string $resource = VisitLogResource::class;
}
