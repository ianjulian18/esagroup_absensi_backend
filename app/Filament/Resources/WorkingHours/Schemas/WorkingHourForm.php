<?php


namespace App\Filament\Resources\WorkingHours\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;

class WorkingHourForm 
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Jam Kerja / Shift')
                ->required()
                ->placeholder('Contoh: Reguler Pagi'),
                
            TextInput::make('code')
                ->label('Kode Shift')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('Contoh: SHIFT-PGI'),

            TimePicker::make('start_time')
                ->label('Jam Masuk')
                ->required()
                ->displayFormat('H:i')
                ->datalist([
                    '08:00', '09:00', '10:00',
                ]),

            TimePicker::make('end_time')
                ->label('Jam Pulang')
                ->required()
                ->displayFormat('H:i')
                ->datalist([
                    '16:00', '17:00', '18:00',
                ]),
        ]);
    }
}