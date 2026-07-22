<?php

namespace App\Filament\Resources\Channels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
