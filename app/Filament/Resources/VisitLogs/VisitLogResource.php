<?php

namespace App\Filament\Resources\VisitLogs;

use App\Filament\Resources\VisitLogs\Pages\CreateVisitLog;
use App\Filament\Resources\VisitLogs\Pages\EditVisitLog;
use App\Filament\Resources\VisitLogs\Pages\ListVisitLogs;
use App\Filament\Resources\VisitLogs\Schemas\VisitLogForm;
use App\Filament\Resources\VisitLogs\Tables\VisitLogsTable;
use App\Models\VisitLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VisitLogResource extends Resource
{
    protected static ?string $model = VisitLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        // Perbaikan di sini: Bungkus array menggunakan $schema->schema()
        return $schema->schema(VisitLogForm::configure());
    }

    public static function table(Table $table): Table
    {
        return VisitLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisitLogs::route('/'),
            'create' => CreateVisitLog::route('/create'),
            'edit' => EditVisitLog::route('/{record}/edit'),
        ];
    }
}
