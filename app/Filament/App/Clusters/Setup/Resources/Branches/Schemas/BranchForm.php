<?php

namespace App\Filament\App\Clusters\Setup\Resources\Branches\Schemas;

use App\Models\PriceList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naziv')
                    ->required(),

                TextInput::make('address')
                    ->label('Adresa'),

                TextInput::make('city')
                    ->label('Grad'),

                TextInput::make('postal_code')
                    ->label('Poštanski broj'),

                TextInput::make('branch_mark')
                    ->required()
                    ->hint('Sekvenca se resetirati ako se promjeni')
                    ->unique(ignoreRecord: true)
                    ->label('Oznaka poslovnice'),

                Select::make('price_list_id')
                    ->label('Primarni cjenik')
                    ->required()
                    ->options(PriceList::pluck('name', 'id')),

                Toggle::make('active')
                    ->default(true)
                    ->inline(false)
                    ->label('Aktivna')
                    ->required(),

            ]);
    }
}
