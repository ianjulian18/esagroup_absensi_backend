<?php

namespace App\Filament\Resources\WorkingHours\Pages;

use App\Filament\Resources\WorkingHours\WorkingHourResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkingHour extends EditRecord
{
    protected static string $resource = WorkingHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
