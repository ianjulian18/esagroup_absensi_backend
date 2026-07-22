<?php

namespace App\Filament\Resources\WorkingGroupSchedules;

use App\Filament\Resources\WorkingGroupSchedules\Pages\CreateWorkingGroupSchedule;
use App\Filament\Resources\WorkingGroupSchedules\Pages\EditWorkingGroupSchedule;
use App\Filament\Resources\WorkingGroupSchedules\Pages\ListWorkingGroupSchedules;
use App\Filament\Resources\WorkingGroupSchedules\Schemas\WorkingGroupScheduleForm;
use App\Filament\Resources\WorkingGroupSchedules\Tables\WorkingGroupSchedulesTable;
use App\Models\WorkingGroupSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkingGroupScheduleResource extends Resource
{
    protected static ?string $model = WorkingGroupSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WorkingGroupScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkingGroupSchedulesTable::configure($table);
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
            'index' => ListWorkingGroupSchedules::route('/'),
            'create' => CreateWorkingGroupSchedule::route('/create'),
            'edit' => EditWorkingGroupSchedule::route('/{record}/edit'),
        ];
    }
}
