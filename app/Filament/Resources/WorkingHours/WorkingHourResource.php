<?php

namespace App\Filament\Resources\WorkingHours;

use App\Filament\Resources\WorkingHours\Pages\CreateWorkingHour;
use App\Filament\Resources\WorkingHours\Pages\EditWorkingHour;
use App\Filament\Resources\WorkingHours\Pages\ListWorkingHours;
use App\Filament\Resources\WorkingHours\Schemas\WorkingHourForm;
use App\Filament\Resources\WorkingHours\Tables\WorkingHoursTable;
use App\Models\WorkingHour;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkingHourResource extends Resource
{
    protected static ?string $model = WorkingHour::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'workingHour';

    public static function form(Schema $schema): Schema
    {
        return WorkingHourForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkingHoursTable::configure($table);
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
            'index' => ListWorkingHours::route('/'),
            'create' => CreateWorkingHour::route('/create'),
            'edit' => EditWorkingHour::route('/{record}/edit'),
        ];
    }
}
