<?php

namespace App\Filament\App\Tables;

use App\Models\Document;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->description(function (Document $record) {
                        return $record->description;
                    })
                    ->label('Naziv')
                    ->searchable(),

                TextColumn::make('creator.full_name')
                    ->label('Dodao'),

                ToggleColumn::make('visible_in_portal')
                    ->label('Prikaz u portalu'),

                TextColumn::make('media_count')
                    ->icon(Heroicon::PaperClip)
                    ->counts('media')
                    ->badge()
                    ->label('Broj datoteka'),

                TextColumn::make('created_at')
                    ->label('Datum kreiranja')
                    ->dateTime()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
