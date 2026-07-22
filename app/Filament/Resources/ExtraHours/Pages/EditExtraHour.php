<?php

namespace App\Filament\Resources\ExtraHours\Pages;

use App\Filament\Resources\ExtraHours\ExtraHourResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExtraHour extends EditRecord
{
    protected static string $resource = ExtraHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
