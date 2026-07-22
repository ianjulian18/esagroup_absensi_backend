<?php

namespace App\Filament\Resources\Baps\Pages;

use App\Filament\Resources\Baps\BapResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBap extends EditRecord
{
    protected static string $resource = BapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
