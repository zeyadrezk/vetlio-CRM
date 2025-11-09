<?php

namespace App\Filament\App\Clusters\Setup\Resources\Rooms\Schemas;

use Awcodes\Palette\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naziv')
                    ->required(),

                TextInput::make('code')
                    ->label('Šifra')
                    ->required(),

                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Poslovnica')
                    ->required(),

                ColorPicker::make('color')
                    ->label('Boja prostorije')
                    ->required(),

                Toggle::make('active')
                    ->label('Aktivna')
                    ->default(true)
            ]);
    }
}
