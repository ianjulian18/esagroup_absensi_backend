<?php

namespace App\Filament\Resources\Leaves\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class LeaveForm
{
    public static function configure(): array
    {
        return [
            Section::make('Informasi Pengajuan Cuti / Izin')
                ->description('Detail form pengajuan yang dikirimkan oleh karyawan.')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Karyawan')
                        ->searchable()
                        ->required(),

                    Select::make('type')
                        ->label('Jenis Pengajuan')
                        ->options([
                            'cuti' => 'Cuti Tahunan',
                            'izin' => 'Izin Tidak Masuk',
                            'sakit' => 'Sakit',
                        ])
                        ->required(),

                    Grid::make(2)->schema([
                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->required(),

                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->required()
                            ->afterOrEqual('start_date'),
                    ]),

                    Textarea::make('reason')
                        ->label('Alasan / Keterangan')
                        ->required()
                        ->columnSpanFull(),

                    FileUpload::make('document_path')
                        ->label('Lampiran Bukti (SKD/Dokumen)')
                        ->disk('public')
                        ->directory('leaves')
                        ->image()
                        ->imagePreviewHeight('250')
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