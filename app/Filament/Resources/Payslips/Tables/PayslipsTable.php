<?php

namespace App\Filament\Resources\Payslips\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action; // Base Action v5

class PayslipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('period')
                    ->label('Periode')
                    ->date('F Y') // Formatnya akan jadi "July 2026"
                    ->sortable(),

                TextColumn::make('net_salary')
                    ->label('Gaji Bersih')
                    ->money('IDR', locale: 'id') // Otomatis format Rp. xxx.xxx
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                // --- TOMBOL PUBLISH CEPAT ---
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(fn ($record) => $record->update(['status' => 'published']))
                    ->requiresConfirmation()
                    ->modalHeading('Publish Slip Gaji?')
                    ->modalDescription('Karyawan akan langsung bisa melihat slip ini di aplikasinya.'),
            ])
            ->defaultSort('period', 'desc');
    }
}