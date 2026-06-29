<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
// Matikan import sementara
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class LocationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Lokasi')->searchable()->sortable(),
                TextColumn::make('code')->label('Kode')->searchable()->sortable(),
                TextColumn::make('sub_area')->label('Sub Area')->searchable(),
                TextColumn::make('timezone')->label('Zona Waktu'),
                TextColumn::make('radius')
                    ->label('Radius (m)')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            
            // Karantina Actions sementara

            ->actions([
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