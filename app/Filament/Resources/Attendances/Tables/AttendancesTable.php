<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class AttendancesTable
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

                TextColumn::make('clock_in')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('clock_out')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'terlambat' => 'warning',
                        'izin' => 'info',
                        'sakit' => 'danger',
                        default => 'gray',
                    }),

                ImageColumn::make('photo_in')
                    ->label('Foto Masuk')
                    ->disk('public')
                    ->circular() // Membuat foto menjadi bulat
                    ->defaultImageUrl(url('/images/no-image.png')), // Opsional jika foto kosong

                ImageColumn::make('photo_out')
                    ->label('Foto Pulang')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png')), // Opsional jika foto kosong

                TextColumn::make('latitude')
                    ->label('Lat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Disembunyikan secara default agar tabel tidak kepanjangan

                TextColumn::make('longitude')
                    ->label('Long')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),    
            ])
            ->filters([
                // Nanti kita bisa tambahkan filter pencarian berdasarkan bulan/tanggal di sini
            ])
            ->actions([
                EditAction::make()
                ->visible(fn (): bool => auth()->user()->role === 'admin'),
                
        
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}