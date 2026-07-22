<?php

namespace App\Filament\Resources\WorkingGroupSchedules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WorkingGroupScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('working_group_id')
                    ->relationship('workingGroup', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('day_of_week')
                    ->required(),
                Select::make('working_hour_id')
                    ->relationship('workingHour', 'name')
                    ->searchable(),
                TextInput::make('late_tolerance')
                    ->required()
                    ->numeric()
                    ->default(15),
                Select::make('routing_type')
                    ->options(['bebas_visit' => 'Bebas visit', 'routing_aktif' => 'Routing aktif'])
                    ->default('bebas_visit')
                    ->required(),
                Select::make('stores')
                    ->multiple()
                    ->options(\App\Models\Store::pluck('name', 'name'))
                    ->searchable()
                    ->placeholder('Pilih toko-toko yang wajib dikunjungi'),
            ]);
    }
}
