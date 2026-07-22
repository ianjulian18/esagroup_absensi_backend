<?php

namespace App\Filament\Resources\ExtraHours\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class ExtraHourForm
{
    public static function configure(): array
    {
        return [
            Section::make('Informasi Pengajuan Lembur')
                ->description('Detail form pengajuan lembur (Extra Hours) karyawan.')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Karyawan')
                        ->searchable()
                        ->required(),

                    DatePicker::make('date')
                        ->label('Tanggal Lembur')
                        ->required(),

                    Grid::make(2)->schema([
                        TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->seconds(false) // Sembunyikan detik agar lebih rapi
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->seconds(false)
                            ->required(),
                    ]),

                    Textarea::make('reason')
                        ->label('Pekerjaan / Alasan Lembur')
                        ->required()
                        ->columnSpanFull(),

                    Select::make('status')
                        ->label('Status Persetujuan')
                        ->options([
                            'pending' => 'Menunggu (Pending)',
                            'approved' => 'Disetujui (Approved)',
                            'rejected' => 'Ditolak (Rejected)',
                        ])
                        ->default('pending')
                        ->required()
                        ->native(false)
                        ->columnSpanFull(),
                ]),
        ];
    }
}