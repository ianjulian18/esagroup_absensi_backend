<?php
namespace App\Filament\Resources\WorkingGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;

class WorkingGroupForm
{
    public static function configure(): array
    {
        return [
            Section::make('Identitas Grup Kerja (Working Group)')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Grup (Misal: Sales Area Jatim)')
                        ->required(),
                    Select::make('region')
                        ->label('Region')
                        ->options(\App\Models\Region::pluck('name', 'name'))
                        ->searchable(),
                    Select::make('area')
                        ->label('Area')
                        ->options(\App\Models\Area::pluck('name', 'name'))
                        ->searchable(),
                    Select::make('sub_area')
                        ->label('Sub Area')
                        ->options(\App\Models\SubArea::pluck('name', 'name'))
                        ->searchable(),
                    DatePicker::make('date_applied')
                        ->label('Tanggal Mulai Berlaku')
                        ->required(),
                ])->columns(2),

            Section::make('Aturan Harian (Days Applied)')
                ->description('Atur jadwal shift, batas toleransi telat, dan rute toko untuk tiap harinya.')
                ->schema([
                    // FITUR SAKTI REPEATER: Mengisi banyak jadwal dalam 1 halaman
                    Repeater::make('schedules')
                        ->relationship() // Otomatis tersambung ke fungsi schedules() di Model
                        ->label('')
                        ->schema([
                            Select::make('day_of_week')
                                ->label('Hari')
                                ->options([
                                    'Senin' => 'Senin',
                                    'Selasa' => 'Selasa',
                                    'Rabu' => 'Rabu',
                                    'Kamis' => 'Kamis',
                                    'Jumat' => 'Jumat',
                                    'Sabtu' => 'Sabtu',
                                    'Minggu' => 'Minggu',
                                ])
                                ->required(),
                            Select::make('working_hour_id')
                                ->relationship('workingHour', 'name')
                                ->label('Jam Kerja (Shift)'), // Memilih shift yang pernah kamu buat sebelumnya
                            TextInput::make('late_tolerance')
                                ->label('Toleransi Telat (Menit)')
                                ->numeric()
                                ->default(15)
                                ->required(),
                            Select::make('routing_type')
                                ->label('Tipe Routing')
                                ->options([
                                    'bebas_visit' => 'Bebas Visit',
                                    'routing_aktif' => 'Routing Aktif (Sesuai Urutan)',
                                ])
                                ->default('bebas_visit')
                                ->required(),
                            Select::make('stores')
                                ->label('Daftar Toko / Lokasi')
                                ->multiple()
                                ->options(\App\Models\Store::pluck('name', 'name'))
                                ->searchable()
                                ->placeholder('Pilih Toko...')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->collapsible() // Bisa dilipat agar tidak memakan layar
                        ->defaultItems(1) // Muncul 1 baris kosong otomatis
                        ->addActionLabel('Tambah Hari Lainnya'),
                ]),
        ];
    }
}