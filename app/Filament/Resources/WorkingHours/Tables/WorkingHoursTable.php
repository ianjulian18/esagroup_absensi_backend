<?php

namespace App\Filament\Resources\WorkingHours\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class WorkingHoursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Shift')->searchable()->sortable(),
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('start_time')
                    ->label('Jam Masuk')
                    ->time('H:i') // Format 24 jam tanpa detik
                    ->badge()
                    ->color('success'),
                TextColumn::make('end_time')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->badge()
                    ->color('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Menggunakan Absolute Path secara langsung
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                ]),
            ]);
    }
}