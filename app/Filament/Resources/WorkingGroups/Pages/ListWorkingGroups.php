<?php

namespace App\Filament\Resources\WorkingGroups\Pages;

use App\Filament\Resources\WorkingGroups\WorkingGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkingGroups extends ListRecords
{
    protected static string $resource = WorkingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
