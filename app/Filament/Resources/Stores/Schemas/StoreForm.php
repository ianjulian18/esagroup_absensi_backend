<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('region_id')
                    ->relationship('region', 'name')
                    ->searchable()
                    ->required(),
                Select::make('area_id')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->required(),
                Select::make('sub_area_id')
                    ->relationship('subArea', 'name')
                    ->searchable()
                    ->required(),
                Select::make('channel_id')
                    ->relationship('channel', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('account_name'),
                TextInput::make('timezone')
                    ->required()
                    ->default('Asia/Jakarta'),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Textarea::make('address')
                    ->columnSpanFull(),
            ]);
    }
}
