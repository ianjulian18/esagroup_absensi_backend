<?php

namespace App\Filament\Resources\SubAreas;

use App\Filament\Resources\SubAreas\Pages\CreateSubArea;
use App\Filament\Resources\SubAreas\Pages\EditSubArea;
use App\Filament\Resources\SubAreas\Pages\ListSubAreas;
use App\Filament\Resources\SubAreas\Schemas\SubAreaForm;
use App\Filament\Resources\SubAreas\Tables\SubAreasTable;
use App\Models\SubArea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubAreaResource extends Resource
{
    protected static ?string $model = SubArea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubAreaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubAreasTable::configure($table);
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
            'index' => ListSubAreas::route('/'),
            'create' => CreateSubArea::route('/create'),
            'edit' => EditSubArea::route('/{record}/edit'),
        ];
    }
}
