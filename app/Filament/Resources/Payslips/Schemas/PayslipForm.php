<?php

namespace App\Filament\Resources\Payslips\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;


class PayslipForm
{
    public static function configure(): array
    {
        return [
            Section::make('Informasi Karyawan & Periode')
                ->description('Pilih karyawan dan bulan/tahun gaji.')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Karyawan')
                        ->searchable()
                        ->required(),
                        
                    DatePicker::make('period')
                        ->label('Periode Gaji (Pilih tanggal 1 pada bulan tersebut)')
                        ->required(),
                ])->columns(2),

            Section::make('Rincian Gaji (Rupiah)')
                ->description('Masukkan nominal tanpa titik/koma. Contoh: 5000000')
                ->schema([
                    TextInput::make('basic_salary')
                        ->label('Gaji Pokok')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    TextInput::make('allowances')
                        ->label('Total Tunjangan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),

                    TextInput::make('overtime_pay')
                        ->label('Uang Lembur')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),

                    TextInput::make('deductions')
                        ->label('Total Potongan (Absen, Kasbon, BPJS)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),

                    TextInput::make('net_salary')
                        ->label('Gaji Bersih (Total yang ditransfer)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                ])->columns(2),

            Section::make('Visibilitas')
                ->schema([
                    Select::make('status')
                        ->label('Status Publikasi')
                        ->options([
                            'draft' => 'Draft (Disembunyikan dari aplikasi Karyawan)',
                            'published' => 'Published (Bisa dilihat & diunduh Karyawan)',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),
                ]),
        ];
    }
}