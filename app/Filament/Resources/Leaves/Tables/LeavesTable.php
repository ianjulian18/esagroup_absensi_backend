<?php

namespace App\Filament\Resources\Leaves\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
// --- PERUBAHAN UTAMA: Menggunakan Unified Base Action ---
use Filament\Actions\Action; 

class LeavesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cuti' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('start_date')
                    ->label('Dari')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Sampai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->wrap()
                    ->limit(50),

                ImageColumn::make('document_path')
                    ->label('Bukti')
                    ->disk('public') // Agar gambar tidak pecah
                    ->circular()
                    // Opsional: Klik gambar untuk buka tab baru (Full Size)
                    ->url(fn ($record) => $record->document_path ? asset('storage/'.$record->document_path) : null)
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
            ->filters([
                // Filter status bisa ditambahkan di sini
            ])
            ->actions([
                // --- TOMBOL SETUJUI ---
                Action::make('approve') // <-- Cukup panggil Action::make
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'approved']))
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan?')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan ini?'),

                // --- TOMBOL TOLAK ---
                Action::make('reject') // <-- Cukup panggil Action::make
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'rejected']))
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan?')
                    ->modalDescription('Apakah Anda yakin ingin menolak pengajuan ini?'),
            ])
            ->bulkActions([
                // Nanti tinggal panggil BulkActionGroup jika diperlukan
            ])
            ->defaultSort('created_at', 'desc');
    }
}