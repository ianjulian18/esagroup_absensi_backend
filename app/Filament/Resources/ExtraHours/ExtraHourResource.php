<?php

namespace App\Filament\Resources\ExtraHours;

use App\Filament\Resources\ExtraHours\Pages\CreateExtraHour;
use App\Filament\Resources\ExtraHours\Pages\EditExtraHour;
use App\Filament\Resources\ExtraHours\Pages\ListExtraHours;
use App\Filament\Resources\ExtraHours\Schemas\ExtraHourForm;
use App\Filament\Resources\ExtraHours\Tables\ExtraHoursTable;
use App\Models\ExtraHour;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExtraHourResource extends Resource
{
    protected static ?string $model = ExtraHour::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ExtraHourForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtraHoursTable::configure($table);
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
            'index' => ListExtraHours::route('/'),
            'create' => CreateExtraHour::route('/create'),
            'edit' => EditExtraHour::route('/{record}/edit'),
        ];
    }
}
