<?php

namespace App\Filament\App\Resources\Reservations\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Models\ReservationReminderDelivery;
use CodeWithDennis\SimpleAlert\Components\SimpleAlert;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                self::headerActions(),
                self::reservationCanceledAlert(),
                self::clientAndPatient(),
                self::mainInformation(),
            ]);
    }

    /**
     * @return SimpleAlert
     */
    public static function reservationCanceledAlert(): SimpleAlert
    {
        return SimpleAlert::make('canceled')
            ->danger()
            ->icon(PhosphorIcons::CalendarMinus)
            ->border()
            ->title('Rezervacije je otkazana')
            ->description(function ($record) {
                $cancelReason = $record->cancel_reason ?? 'Nema razloga';

                return "Razlog otkazivanja: $cancelReason";
            })
            ->columnSpanFull();
    }

    /**
     * @return Grid
     */
    public static function mainInformation(): Grid
    {
        return Grid::make(3)
            ->columnSpanFull()
            ->schema([
                TextEntry::make('date')
                    ->icon(PhosphorIcons::Calendar)
                    ->date('d.m.Y')
                    ->label('Datum'),

                TextEntry::make('from')
                    ->icon(PhosphorIcons::Clock)
                    ->state(function ($record) {
                        $diff = $record->from->diffInMinutes($record->to);
                        return $record->from->format('H:i') . ' - ' . $record->to->format('H:i') . ' (' . $diff . ' min)';
                    })
                    ->label('Trajanje'),

                TextEntry::make('service.name')
                    ->icon(PhosphorIcons::Hand)
                    ->state(function ($record) {
                        return $record->service->name . ' (' . Number::currency($record->service->currentPrice->price_with_vat) . ')';
                    })
                    ->label('Usluga'),

                TextEntry::make('serviceProvider.full_name')
                    ->icon(PhosphorIcons::User)
                    ->label('Liječnik'),

                TextEntry::make('room.name')
                    ->icon(PhosphorIcons::Bed)
                    ->label('Prostorija'),

                TextEntry::make('note')
                    ->columnSpanFull()
                    ->default('-')
                    ->label('Napomena')
                    ->icon(PhosphorIcons::Note),
            ]);
    }

    private static function clientAndPatient()
    {
        return Grid::make(2)
            ->columnSpanFull()
            ->schema([
                Fieldset::make('Klijent')
                    ->schema([
                        Flex::make([
                            ImageEntry::make('client.avatar_url')
                                ->circular()
                                ->imageSize(100)
                                ->hiddenLabel(),
                            Grid::make(1)
                                ->gap(false)
                                ->schema([
                                    TextEntry::make('client.full_name')
                                        ->hiddenLabel()
                                        ->size(TextSize::Large),
                                    TextEntry::make('client.email')
                                        ->icon(PhosphorIcons::Envelope)
                                        ->size(TextSize::Small)
                                        ->hiddenLabel(),
                                    TextEntry::make('client.phone')
                                        ->icon(PhosphorIcons::Phone)
                                        ->size(TextSize::Small)
                                        ->hiddenLabel(),
                                ])
                        ])->gap(false)->columnSpanFull(),
                    ]),

                Fieldset::make('Pacijent')
                    ->schema([
                        Flex::make([
                            ImageEntry::make('patient.avatar_url')
                                ->defaultImageUrl('https://www.svgrepo.com/show/420337/animal-avatar-bear.svg')
                                ->circular()
                                ->imageSize(100)
                                ->hiddenLabel(),
                            Grid::make(1)
                                ->gap(false)
                                ->schema([
                                    TextEntry::make('patient.name')
                                        ->hiddenLabel()
                                        ->size(TextSize::Large),
                                    TextEntry::make('patient.species.name')
                                        ->icon(PhosphorIcons::Dog)
                                        ->size(TextSize::Small)
                                        ->hiddenLabel(),
                                    TextEntry::make('patient.breed.name')
                                        ->icon(PhosphorIcons::Cow)
                                        ->size(TextSize::Small)
                                        ->hiddenLabel(),
                                ])
                        ])->gap(false)->columnSpanFull(),
                    ])

            ]);
    }

    private static function headerActions()
    {
        return Flex::make([
            EditAction::make()
                ->outlined(),
            Action::make('cancel-reservation')
                ->outlined()
                ->label('Otkaži rezervaciju')
                ->color('danger')
                ->icon(PhosphorIcons::CalendarMinus)
        ]);
    }
}
