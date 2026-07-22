<?php

namespace App\Filament\Resources\Baps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class BapForm
{
    public static function configure(): array
    {
        return [
            Section::make('Informasi Berita Acara Presensi (BAP)')
                ->description('Detail form pengajuan absen manual oleh karyawan.')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Karyawan')
                        ->searchable()
                        ->required(),

                    Grid::make(2)->schema([
                        DatePicker::make('date')
                            ->label('Tanggal Terlewat')
                            ->required(),

                        Select::make('type')
                            ->label('Jenis Absen')
                            ->options([
                                'masuk' => 'Absen Masuk',
                                'pulang' => 'Absen Pulang',
                            ])
                            ->required(),
                    ]),

                    TimePicker::make('time')
                        ->label('Jam Seharusnya')
                        ->seconds(false)
                        ->required(),

                    Textarea::make('reason')
                        ->label('Alasan Lupa / Gagal Absen')
                        ->required()
                        ->columnSpanFull(),

                    FileUpload::make('proof_path')
                        ->label('Lampiran Bukti (Foto Lokasi/Kegiatan)')
                        ->disk('public')
                        ->directory('baps')
                        ->image()
                        ->imagePreviewHeight('250')
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