<?php

namespace App\Filament\App\Clusters\Setup\Resources\PriceLists\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PriceListInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Naziv'),

                IconEntry::make('active')
                    ->label('Aktivan')
                    ->boolean(),

                TextEntry::make('created_at')
                    ->label('Datum kreiranja')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('Datum izmjene')
                    ->placeholder('-'),
            ]);
    }
}
