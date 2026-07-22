<?php

namespace App\Filament\Resources\ExtraHours\Pages;

use App\Filament\Resources\ExtraHours\ExtraHourResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtraHours extends ListRecords
{
    protected static string $resource = ExtraHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
