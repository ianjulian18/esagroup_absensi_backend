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

                // TextInput::make('password')
                //     ->password()
                //     ->required(fn (string $operation): bool => $operation === 'create')
                //     ->dehydrated(fn (?string $state) => filled($state))
                //     ->label('Password (Isi jika ubah/baru)'),

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn (string $context): bool => $context === 'create') // Hanya wajib diisi saat buat user baru
                    ->minLength(8)
                    ->confirmed() // Ini adalah perintah sakti untuk mencocokkan dengan kolom di bawahnya
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state)),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Konfirmasi Password')
                    ->required(fn (string $context): bool => $context === 'create') // Hanya wajib saat buat user baru
                    ->minLength(8)
                    ->dehydrated(false), // Perintah penting agar kolom konfirmasi ini TIDAK ikut disimpan ke database MySQL

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
            ]);
    }
}