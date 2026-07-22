<?php
namespace App\Filament\Resources\VisitLogs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class VisitLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('store_name')
                    ->label('Lokasi Kunjungan')
                    ->searchable(),
                TextColumn::make('issue')
                    ->label('Issue')
                    ->wrap()
                    ->limit(40), // Membatasi teks agar tabel tidak terlalu lebar
                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    // Mewarnai badge sesuai permintaan PDF Atasan
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',         // Merah
                        'action_taken' => 'info',   // Biru
                        'completed' => 'success',   // Hijau
                        'overdue' => 'warning',     // Oranye
                        default => 'gray',
                    }),
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
                    ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}