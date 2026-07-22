<?php

namespace App\Filament\Resources\WorkingGroups;

use App\Filament\Resources\WorkingGroups\Pages\CreateWorkingGroup;
use App\Filament\Resources\WorkingGroups\Pages\EditWorkingGroup;
use App\Filament\Resources\WorkingGroups\Pages\ListWorkingGroups;
use App\Filament\Resources\WorkingGroups\Schemas\WorkingGroupForm;
use App\Filament\Resources\WorkingGroups\Tables\WorkingGroupsTable;
use App\Models\WorkingGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkingGroupResource extends Resource
{
    protected static ?string $model = WorkingGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(WorkingGroupForm::configure());
    }

    public static function table(Table $table): Table
    {
        return WorkingGroupsTable::configure($table);
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
            'index' => ListWorkingGroups::route('/'),
            'create' => CreateWorkingGroup::route('/create'),
            'edit' => EditWorkingGroup::route('/{record}/edit'),
        ];
    }
}
