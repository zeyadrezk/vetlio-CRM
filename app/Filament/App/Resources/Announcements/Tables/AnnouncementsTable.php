<?php

namespace App\Filament\App\Resources\Announcements\Tables;

use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Filament\Shared\Columns\UpdatedAtColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->tooltip(function ($record) {
                        return new HtmlString($record->content);
                    })
                    ->searchable()
                    ->label('Title'),

                TextColumn::make('user.full_name')
                    ->label('Created by')
                    ->searchable(),

                IconColumn::make('for_users')
                    ->label('For users')
                    ->alignCenter()
                    ->boolean(),

                IconColumn::make('for_clients')
                    ->label('For clients')
                    ->alignCenter()
                    ->boolean(),

                TextColumn::make('starts_at')
                    ->label('Visible from')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Visible until')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('dismissed_by_clients_count')
                    ->alignRight()
                    ->counts('dismissedByClients')
                    ->badge()
                    ->label('Views from clients'),

                TextColumn::make('dismissed_by_users_count')
                    ->alignRight()
                    ->counts('dismissedByUsers')
                    ->badge()
                    ->label('Views from users'),


                CreatedAtColumn::make('created_at'),
                UpdatedAtColumn::make('updated_at'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->disabled(function ($record) {
                        return $record->dismissedByClients()->exists() || $record->dismissedByUsers()->exists();
                    }),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
