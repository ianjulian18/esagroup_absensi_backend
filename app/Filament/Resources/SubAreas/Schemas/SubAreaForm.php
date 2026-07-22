<?php

namespace App\Filament\Resources\SubAreas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;

class SubAreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('area_id')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
