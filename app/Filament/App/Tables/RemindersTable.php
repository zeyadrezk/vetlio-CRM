<?php

namespace App\Filament\App\Tables;

use App\Models\Reminder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RemindersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Naslov')
                    ->searchable(),

                TextColumn::make('remind_at')
                    ->label('Datum i vrijeme podsjetnika')
                    ->dateTime('d.m.Y H:i'),

                TextColumn::make('userToRemind.full_name')
                    ->label('Podsjetnik za'),

                IconColumn::make('send_email')
                    ->alignCenter()
                    ->label('Pošalji mail')
                    ->boolean(),

                IconColumn::make('email_sent_at')
                    ->label('Obaviješten?')
                    ->tooltip(function (Reminder $reminder) {
                        if ($reminder->email_sent_at) {
                            return $reminder->email_sent_at->format('d.m.Y H:i');
                        }
                        return null;
                    })
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(function (Reminder $record) {
                        return !$record->isNotified();
                    }),

                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
