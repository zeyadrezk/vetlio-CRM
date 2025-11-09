<?php

namespace App\Filament\App\Resources\Invoices\Tables;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\PaymentMethod;
use App\Filament\App\Actions\ClientCardAction;
use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Models\Client;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->sortable()
                    ->icon(function($record) {
                        return $record->fiscalization_at ? PhosphorIcons::CheckCircleBold : PhosphorIcons::XCircleBold;
                    })
                    ->iconColor(function($record) {
                        return $record->fiscalization_at ? 'success' : 'danger';
                    })
                    ->tooltip(function($record) {
                        return $record->fiscalization_at ? 'Račun je uspješno fiskaliziran' : 'Račun nije fiskaliziran';
                    })
                    ->searchable()
                    ->label('Šifra'),

                TextColumn::make('branch.name')
                    ->sortable()
                    ->searchable()
                    ->label('Poslovnica'),

                TextColumn::make('client.full_name')
                    ->sortable()
                    ->searchable()
                    ->label('Klijent')
                    ->icon(PhosphorIcons::User),

                TextColumn::make('invoice_date')
                    ->sortable()
                    ->sortable()
                    ->date()
                    ->label('Datum računa'),

                TextColumn::make('payment_method_id')
                    ->sortable()
                    ->label('Način plaćanja'),

                TextColumn::make('user.full_name')
                    ->sortable()
                    ->searchable()
                    ->sortable()
                    ->label('Kreirao'),

                TextColumn::make('total_base_price')
                    ->label('Osnovica')
                    ->numeric(2)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->suffix(' EUR')
                    ->color(function ($record) {
                        return $record->storno_of_id ? 'danger' : null;
                    }),

                TextColumn::make('total_tax')
                    ->label('PDV iznos')
                    ->numeric(2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->suffix(' EUR')
                    ->color(function ($record) {
                        return $record->storno_of_id ? 'danger' : null;
                    }),

                TextColumn::make('total_discount')
                    ->label('Popust')
                    ->numeric(2)
                    ->sortable()
                    ->suffix(' EUR')
                    ->color(function ($record) {
                        return $record->storno_of_id ? 'danger' : null;
                    }),

                TextColumn::make('total')
                    ->label('Ukupno')
                    ->numeric(2)
                    ->sortable()
                    ->suffix(' EUR')
                    ->color(function ($record) {
                        return $record->storno_of_id ? 'danger' : null;
                    })
                    ->weight(FontWeight::Bold),

                CreatedAtColumn::make('created_at'),
            ])
            ->filters([
                TernaryFilter::make('storno_of_id')
                    ->label('Stornirani')
                    ->nullable(),

                SelectFilter::make('payment_method_id')
                    ->label('Način plaćanja')
                    ->native(false)
                    ->multiple()
                    ->options(PaymentMethod::class),

                SelectFilter::make('user_id')
                    ->multiple()
                    ->label('Izradio')
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn(User $record) => $record->full_name)
                    ->native(false),

                SelectFilter::make('client_id')
                    ->multiple()
                    ->label('Klijent')
                    ->relationship('client', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn(Client $record) => $record->full_name)
                    ->native(false)
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->slideOver()
                    ->label('Filter'),
            )
            ->recordActions([
                ViewAction::make(),
                ClientCardAction::make()
            ]);
    }
}
