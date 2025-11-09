<?php

namespace App\Filament\App\Resources\Tasks\Tables;

use App\Enums\TaskStatus;
use App\Filament\Columns\PriorityColumn;
use App\Filament\Shared\Columns\CreatedAtColumn;
use App\Filament\Shared\Columns\UpdatedAtColumn;
use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->with(['related']);
            })
            ->columns([
                TextColumn::make('title')
                    ->icon(function (Task $record) {
                        return $record->hasMedia() ? Heroicon::PaperClip : null;
                    })
                    ->label('Naziv')
                    ->description(function (Task $record) {
                        return $record->related->relatedLabel() . ': ' . $record->related->relatedValue();
                    })
                    ->weight(FontWeight::Medium)
                    ->searchable(),

                TextColumn::make('user.full_name')
                    ->label('Kreirao')
                    ->searchable(),

                TextColumn::make('related.name')
                    ->hidden()
                    ->label('Vezan za')
                    ->searchable(),

                TextColumn::make('status_id')
                    ->formatStateUsing(function($state) {
                        return TaskStatus::from($state)->getLabel();
                    })
                    ->badge()
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('assignedUsers.full_name')
                    ->limitList()
                    ->label('Dodjeljeno'),

                TextColumn::make('start_at')
                    ->label('Početak')
                    ->date()
                    ->sortable(),

                TextColumn::make('deadline_at')
                    ->label('Rok za završetak')
                    ->description(function (
                        Task $record) {
                        return $record?->deadline_at?->diffForHumans();
                    })
                    ->date()
                    ->sortable(),

                SpatieTagsColumn::make('tags')
                    ->label('Oznake'),

                CreatedAtColumn::make('created_at'),
                UpdatedAtColumn::make('updated_at')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
