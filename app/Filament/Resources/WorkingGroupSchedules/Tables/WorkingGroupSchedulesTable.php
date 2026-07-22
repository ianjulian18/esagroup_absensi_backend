<?php

namespace App\Filament\Resources\WorkingGroupSchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkingGroupSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workingGroup.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('day_of_week')
                    ->searchable(),
                TextColumn::make('workingHour.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('late_tolerance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('routing_type')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
