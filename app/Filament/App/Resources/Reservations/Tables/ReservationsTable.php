<?php

namespace App\Filament\App\Resources\Reservations\Tables;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\ReservationStatus;
use App\Filament\App\Actions\ClientCardAction;
use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Filament\App\Resources\Reservations\Actions\MoveBack;
use App\Filament\App\Resources\Reservations\Actions\MoveRight;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('client.full_name')
                    ->searchable()
                    ->sortable()
                    ->label('Klijent'),

                TextColumn::make('patient.name')
                    ->searchable()
                    ->sortable()
                    ->description(function ($record) {
                        return $record->patient->breed->name . ', ' . $record->patient->species->name;
                    })
                    ->label('Pacijent'),

                TextColumn::make('from')
                    ->sortable()
                    ->date()
                    ->description(function ($record) {
                        return $record->from->format('H:i') . ' - ' . $record->to->format('H:i');
                    })
                    ->label('Vrijeme rezervacije'),

                TextColumn::make('service.name')
                    ->searchable()
                    ->sortable()
                    ->description(function ($record) {
                        return $record->service->duration->format('i') . ' min';
                    })
                    ->label('Usluga'),

                TextColumn::make('serviceProvider.full_name')
                    ->searchable()
                    ->label('Liječnik'),

                TextColumn::make('room.name')
                    ->label('Prostorija'),
            ])
            ->persistFiltersInSession()
            ->deferFilters(false)
            ->filtersFormColumns(3)
            ->filters(self::getFilters(), layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions(self::getRecordActions())
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getFilters(): array
    {
        return [
            Filter::make('date')
                ->default()
                ->columns(2)
                ->schema([
                    DatePicker::make('from')
                        ->label('Od')
                        ->default(now()->startOfDay()),
                    DatePicker::make('to')
                        ->label('Do')
                        ->default(now()->endOfDay()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                        )
                        ->when(
                            $data['to'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                        );
                }),

            SelectFilter::make('service_provider_id')
                ->native(false)
                ->relationship('serviceProvider', 'first_name')
                ->label('Liječnik'),
            SelectFilter::make('room_id')
                ->native(false)
                ->relationship('room', 'name')
                ->label('Prostorija')
        ];
    }

    /**
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            MoveBack::make('back')
                ->visible(function ($record) {
                    return $record->status_id > ReservationStatus::Ordered->value;
                }),
            MoveRight::make('right')
                ->visible(function ($record) {
                    return $record->status_id < ReservationStatus::Completed->value;
                }),
            Action::make('create-medical-document')
                ->label('Kreiraj nalaz')
                ->icon(PhosphorIcons::FilePlus)
                ->visible(function ($record) {
                    return $record->status_id == ReservationStatus::InProcess->value;
                })
                ->url(function ($record) {
                    return MedicalDocumentResource::getUrl('create', ['reservationId' => $record->uuid]);
                }),
            ViewAction::make(),
            ActionGroup::make([
                ClientCardAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ]),
        ];
    }
}
