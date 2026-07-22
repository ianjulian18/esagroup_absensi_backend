<?php
namespace App\Filament\Resources\VisitLogs\Schemas;

// INI ADALAH BARIS IMPORT YANG SEMPAT HILANG
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;


class VisitLogForm
{
    public static function configure(): array
    {
        return [
            Section::make('Detail Kunjungan')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Karyawan')
                        ->required()
                        ->searchable(),
                    TextInput::make('store_name')
                        ->label('Lokasi / Nama Toko')
                        ->required(),
                ])->columns(2),
                
            Section::make('Laporan Aktivitas (Log)')
                ->schema([
                    Textarea::make('issue')
                        ->label('Issue (Masalah yang ditemukan)')
                        ->required(),
                    Textarea::make('action')
                        ->label('Action (Tindakan yang diambil)')
                        ->required(),
                    TextInput::make('target')
                        ->label('Target Penyelesaian')
                        ->required(),
                    TextInput::make('actual')
                        ->label('Actual (Hasil saat ini)')
                        ->required(),
                    DatePicker::make('deadline')
                        ->label('Deadline')
                        ->required(),
                    Textarea::make('notes')
                        ->label('Catatan Tambahan'),
                ])->columns(2),

            Section::make('Status Laporan')
                ->schema([
                    Select::make('status')
                        ->label('Tracking Status')
                        ->options([
                            'open' => 'Open Issue (Merah)',
                            'action_taken' => 'Action Taken (Biru)',
                            'completed' => 'Completed (Hijau)',
                            'overdue' => 'Overdue (Oranye)',
                        ])
                        ->default('open')
                        ->required()
                        ->native(false),
                ]),
            
        ];
    }
}