<?php

namespace App\Filament\Resources\Baps;

use App\Filament\Resources\Baps\Pages\CreateBap;
use App\Filament\Resources\Baps\Pages\EditBap;
use App\Filament\Resources\Baps\Pages\ListBaps;
use App\Filament\Resources\Baps\Schemas\BapForm;
use App\Filament\Resources\Baps\Tables\BapsTable;
use App\Models\Bap;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BapResource extends Resource
{
    protected static ?string $model = Bap::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BapForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BapsTable::configure($table);
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
            'index' => ListBaps::route('/'),
            'create' => CreateBap::route('/create'),
            'edit' => EditBap::route('/{record}/edit'),
        ];
    }
}
