<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class LocationForm 
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Lokasi/Kantor')
                ->required()
                ->placeholder('Contoh: Arina Surabaya'),
                
            TextInput::make('code')
                ->label('Kode Lokasi')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('Contoh: ARN-SBY'),

            TextInput::make('sub_area')
                ->label('Sub Area (Opsional)')
                ->placeholder('Contoh: Jawa Timur'),

            Select::make('timezone')
                ->label('Zona Waktu')
                ->options([
                    'Asia/Jakarta' => 'WIB (Asia/Jakarta)',
                    'Asia/Makassar' => 'WITA (Asia/Makassar)',
                    'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
                ])
                ->default('Asia/Jakarta')
                ->required()
                ->searchable(),

            Textarea::make('address')
                ->label('Alamat Lengkap')
                ->columnSpanFull(),
                
            TextInput::make('latitude')
                ->label('Latitude')
                ->numeric()
                ->required()
                ->placeholder('Contoh: -7.331234'),
                
            TextInput::make('longitude')
                ->label('Longitude')
                ->numeric()
                ->required()
                ->placeholder('Contoh: 112.731234'),
                
            TextInput::make('radius')
                ->label('Radius Gembok (Meter)')
                ->numeric()
                ->default(50)
                ->required(),
        ]);
    }
}