<?php

namespace App\Filament\Resources\WorkingGroups\Pages;

use App\Filament\Resources\WorkingGroups\WorkingGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkingGroup extends EditRecord
{
    protected static string $resource = WorkingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
