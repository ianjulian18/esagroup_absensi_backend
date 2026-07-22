<?php

namespace App\Filament\Resources\WorkingGroupSchedules\Pages;

use App\Filament\Resources\WorkingGroupSchedules\WorkingGroupScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkingGroupSchedules extends ListRecords
{
    protected static string $resource = WorkingGroupScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
