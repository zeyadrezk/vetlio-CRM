<?php

namespace App\Filament\Tables;

use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsToSelectTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->query(Service::query()->whereHas('currentPrice'))
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label('Šifra'),

                TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable(),

                TextColumn::make('serviceGroup.name')
                    ->label('Grupa')
                    ->searchable(),

                TextColumn::make('currentPrice.price')
                    ->money('EUR')
                    ->numeric(2)
                    ->alignRight()
                    ->label('Cijena'),

                TextColumn::make('currentPrice.vat_percentage')
                    ->numeric(2)
                    ->alignRight()
                    ->suffix('%')
                    ->label('PDV'),

                TextColumn::make('currentPrice.price_with_vat')
                    ->alignRight()
                    ->money('EUR')
                    ->weight(FontWeight::Bold)
                    ->label('Cijena sa PDV'),
            ])
            ->filters([
                SelectFilter::make('service_group_id')
                    ->label('Grupa')
                    ->relationship('serviceGroup', 'name')
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
