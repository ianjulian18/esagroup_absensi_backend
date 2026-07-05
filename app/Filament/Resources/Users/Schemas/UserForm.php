<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                // --- TAMBAHAN BARU: NIK & NIP ---
                TextInput::make('nik')
                    ->label('NIK (Nomor Induk Kependudukan)')
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('nip')
                    ->label('NIP (Nomor Induk Pegawai)')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                // --------------------------------

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->confirmed()
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Konfirmasi Password')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->dehydrated(false),

                Select::make('role')
                    ->label('Jabatan')
                    ->options([
                        'admin' => 'Admin',
                        'karyawan' => 'Karyawan',
                    ])
                    ->required()
                    ->default('karyawan'),

                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->label('Penempatan Cabang/Lokasi'),

                Select::make('working_hour_id')
                    ->relationship('workingHour', 'name')
                    ->label('Jam Kerja (Shift)'),

                Toggle::make('is_location_locked')
                    ->label('Kunci Lokasi (Geofencing)')
                    ->default(true),

                Toggle::make('is_resign')
                    ->label('Tandai Karyawan Resign')
                    ->default(false),
            ]);
    }
}