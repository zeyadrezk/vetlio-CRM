<?php

namespace App\Filament\App\Resources\Payments\Tables;

use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Filament\Shared\Columns\UpdatedAtColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Šifra')
                    ->searchable(),

                TextColumn::make('branch.name')
                    ->label('Poslovnica')
                    ->sortable(),

                TextColumn::make('invoice.code')
                    ->searchable()
                    ->label('Račun'),

                TextColumn::make('user.full_name')
                    ->label('Kreirao')
                    ->sortable(),

                TextColumn::make('payment_method_id')
                    ->label('Način plaćanja')
                    ->sortable(),

                TextColumn::make('client.full_name')
                    ->searchable()
                    ->label('Klijent'),

                TextColumn::make('payment_at')
                    ->dateTime()
                    ->label('Datum uplate')
                    ->sortable(),

                TextColumn::make('amount')
                    ->summarize(Sum::make()->money('EUR', 100)->label('Ukupan iznos'))
                    ->label('Iznos')
                    ->numeric(2)
                    ->sortable()
                    ->suffix(' EUR')
                    ->color(function ($record) {
                        return $record->storno_of_id ? 'danger' : 'success';
                    })
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                CreatedAtColumn::make('created_at'),
                UpdatedAtColumn::make('updated_at'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
