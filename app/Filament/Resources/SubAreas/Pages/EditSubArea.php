<?php

namespace App\Filament\Resources\SubAreas\Pages;

use App\Filament\Resources\SubAreas\SubAreaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubArea extends EditRecord
{
    protected static string $resource = SubAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
