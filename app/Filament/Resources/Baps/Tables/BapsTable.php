<?php

namespace App\Filament\Resources\Baps\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\Action; // <-- Base Action v5

class BapsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'info',
                        'pulang' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('time')
                    ->label('Jam')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->wrap()
                    ->limit(30),

                ImageColumn::make('proof_path')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->url(fn ($record) => $record->proof_path ? asset('storage/'.$record->proof_path) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                // --- TOMBOL SETUJUI ---
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'approved']))
                    ->requiresConfirmation()
                    ->modalHeading('Setujui BAP?'),

                // --- TOMBOL TOLAK ---
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'rejected']))
                    ->requiresConfirmation()
                    ->modalHeading('Tolak BAP?'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}