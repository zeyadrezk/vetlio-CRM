<?php

namespace App\Filament\App\Resources\MedicalDocuments\Tables;

use App\Filament\App\Actions\ClientCardAction;
use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Filament\Shared\Columns\UpdatedAtColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicalDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Šifra')
                    ->tooltip(function ($record) {
                        return $record->locked_at ? 'Nalaz je zaključan' : null;
                    })
                    ->icon(function ($record) {
                        return $record->locked_at ? Heroicon::LockClosed : null;
                    })
                    ->iconColor('danger')
                    ->searchable(),

                TextColumn::make('reservation.from')
                    ->sortable()
                    ->dateTime('d.m.Y H:i')
                    ->label('Rezervacija'),

                TextColumn::make('patient.name')
                    ->label('Pacijent')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('client.full_name')
                    ->label('Klijent')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('reason_for_coming')
                    ->label('Razlog za dolazak')
                    ->searchable(),

                TextColumn::make('serviceProvider.name')
                    ->label('Liječnik')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('items_sum_total')
                    ->money('EUR', 100)
                    ->label('Ukupno')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->sum('items', 'total'),

                CreatedAtColumn::make('created_at'),
                UpdatedAtColumn::make('updated_at'),
            ])
            ->recordActions([
                ViewAction::make(),
                ClientCardAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(auth()->user()->administrator),
            ]);
    }
}
