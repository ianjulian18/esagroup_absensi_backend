<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Nama Karyawan')
                    ->required()
                    ->searchable(),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),

                TimePicker::make('clock_in')
                    ->label('Jam Masuk')
                    ->seconds(false), // Menghilangkan input detik agar simpel

                TimePicker::make('clock_out')
                    ->label('Jam Pulang')
                    ->seconds(false),

                Select::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'terlambat' => 'Terlambat',
                    ])
                    ->required()
                    ->default('hadir'),
            ]);
    }
}