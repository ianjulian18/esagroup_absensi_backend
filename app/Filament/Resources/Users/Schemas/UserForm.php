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

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(6)
                    ->confirmed()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state)),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Konfirmasi Password')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(6)
                    ->dehydrated(false),

                Select::make('role')
                    ->label('Jabatan')
                    ->options([
                        'admin' => 'Admin',
                        'karyawan' => 'Karyawan',
                    ])
                    ->required()
                    ->default('karyawan'),

                Toggle::make('is_resign')
                    ->label('Tandai Karyawan Resign')
                    ->default(false),

                // --- TAMBAHAN BARU ---
                // Dipasang langsung tanpa pembungkus agar terhindar dari Error Class Not Found
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->label('Penempatan Kantor')
                    ->searchable()
                    ->preload()
                    ->visible(fn (string $context): bool => $context === 'edit') // HANYA TAMPIL SAAT EDIT
                    ->required(fn (string $context): bool => $context === 'edit'),

                Select::make('working_hour_id')
                    ->relationship('workingHour', 'name')
                    ->label('Shift Jam Kerja')
                    ->searchable()
                    ->preload()
                    ->visible(fn (string $context): bool => $context === 'edit') // HANYA TAMPIL SAAT EDIT
                    ->required(fn (string $context): bool => $context === 'edit'),

                Toggle::make('is_location_locked')
                    ->label('Kunci Lokasi (Geofencing)')
                    ->default(true)
                    ->helperText('Jika aktif, karyawan HANYA bisa absen di dalam radius kantor penempatannya.')
                    ->visible(fn (string $context): bool => $context === 'edit'), // HANYA TAMPIL SAAT EDIT
            ]);
    }
}