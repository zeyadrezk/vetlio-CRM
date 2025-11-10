<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Reservations\Schemas\ReservationForm;
use App\Filament\App\Resources\Reservations\Tables\ReservationsTable;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class PatientReservations extends ManageRelatedRecords
{
    protected static string $resource = PatientResource::class;

    protected static string $relationship = 'reservations';

    protected static string|BackedEnum|null $navigationIcon = PhosphorIcons::CalendarPlus;

    protected static ?string $title = 'Reservations';

    protected static ?string $navigationLabel = 'Reservations';

    public function getSubheading(): string|Htmlable|null
    {
        return $this->getRecord()->description;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
        ];
    }

    public function form(Schema $schema): Schema
    {
        return ReservationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ReservationsTable::configure($table)
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(function($record) {
                        return !$record->canceled_at;
                    })
            ])
            ->filters([]);
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge(function (Builder $query) {
                    return $this->getRecord()->reservations_count;
                })
                ->icon(PhosphorIcons::Calendar)
                ->label('All'),
            'active' => Tab::make()
                ->label('Active')
                ->badge(function (Builder $query) {
                    return $this->getRecord()->reservations()->canceled(false)->count();
                })
                ->icon(PhosphorIcons::CalendarCheck)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('canceled', false)),
            'canceled' => Tab::make()
                ->label('Canceled')
                ->badgeColor('danger')
                ->badge(function (Builder $query) {
                    return $this->getRecord()->reservations()->canceled(false)->count();
                })
                ->icon(PhosphorIcons::CalendarX)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('canceled', true)),
        ];
    }
}
