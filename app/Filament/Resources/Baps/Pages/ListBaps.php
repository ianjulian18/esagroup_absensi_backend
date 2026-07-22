<?php

namespace App\Filament\Resources\Baps\Pages;

use App\Filament\Resources\Baps\BapResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBaps extends ListRecords
{
    protected static string $resource = BapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
