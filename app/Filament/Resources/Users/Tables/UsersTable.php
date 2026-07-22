<?php

namespace App\Filament\Resources\Users\Tables;

// PERBAIKAN NAMESPACE: Menggunakan Tables\Actions
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->color('success')
                    ->label('Jabatan (Roles)'),

                // --- TAMBAHAN BARU: Menampilkan master data di tabel ---
                TextColumn::make('location.name')
                    ->label('Penempatan')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('workingHour.name')
                    ->label('Jam Kerja')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                IconColumn::make('is_resign')
                    ->boolean()
                    ->label('Status Resign'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}