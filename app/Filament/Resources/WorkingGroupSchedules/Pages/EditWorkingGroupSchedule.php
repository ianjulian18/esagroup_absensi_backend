<?php

namespace App\Filament\Resources\WorkingGroupSchedules\Pages;

use App\Filament\Resources\WorkingGroupSchedules\WorkingGroupScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkingGroupSchedule extends EditRecord
{
    protected static string $resource = WorkingGroupScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
