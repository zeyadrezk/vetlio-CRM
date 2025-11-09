<?php

namespace App\Filament\App\Resources\Reservations\Schemas;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\App\Resources\Clients\Schemas\ClientForm;
use App\Models\Client;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Klijent')
                    ->options(Client::pluck('first_name', 'id'))
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                    ->createOptionForm(function ($schema) {
                        return ClientForm::configure($schema);
                    })
                    ->createOptionUsing(function ($data) {
                        return Client::create($data)->getKey();
                    })
                    ->required()
                    ->prefixIcon(PhosphorIcons::User)
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $set('patient_id', null);
                    }),

                Select::make('patient_id')
                    ->prefixIcon(PhosphorIcons::Dog)
                    ->label('Pacijent')
                    ->required()
                    ->live()
                    ->disabled(function ($get) {
                        return !$get('client_id');
                    })
                    ->options(function (Get $get) {
                        $patients = Patient::query();
                        if ($get('client_id')) {
                            $patients->where('client_id', $get('client_id'));
                        }

                        return $patients->pluck('name', 'id');
                    }),


                Select::make('service_id')
                    ->label('Usluga')
                    ->live()
                    ->required()
                    ->options(function (Get $get) {
                        $services = Service::whereHas('currentPrice');
                        if ($get('service_provider_id')) {
                            $services->whereHas('users', function ($query) use ($get) {
                                $query->where('user_id', $get('service_provider_id'));
                            });
                        }
                        return $services->pluck('name', 'id');
                    })
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $startTime = $get('from');
                        if ($startTime) {
                            $totalMinutes = Service::find($state)->duration->minute;
                            $endTime = Carbon::parse($startTime)->addMinutes($totalMinutes);
                            $set('to', $endTime);
                        }
                    }),


                Select::make('service_provider_id')
                    ->label('Liječnik')
                    ->disabled(function ($get) {
                        return !$get('service_id');
                    })
                    ->required()
                    ->options(function (Get $get) {
                        $users = User::query();
                        if ($get('service_id')) {
                            $users->whereHas('services', function ($query) use ($get) {
                                $query->where('user_id', $get('user_id'));
                            });
                        }
                        return $users->pluck('first_name', 'id');
                    })
                    ->prefixIcon(PhosphorIcons::UserPlus)
                    ->live()
                    ->options(User::whereServiceProvider(true)->pluck('first_name', 'id'))
                    ->afterStateUpdated(fn($state, $get, $set) => self::checkAvailability($get, $set)),


                Select::make('room_id')
                    ->required()
                    ->prefixIcon(PhosphorIcons::Bed)
                    ->disabled(function ($get) {
                        return !$get('service_id');
                    })
                    ->options(function (Get $get) {
                        $rooms = Room::query();
                        if ($get('service_id')) {
                            $rooms->whereHas('services', function ($query) use ($get) {
                                $query->where('service_id', $get('service_id'));
                            });
                        }
                        return $rooms->pluck('name', 'id');
                    })
                    ->label('Prostorija')
                    ->live(false, 500)
                    ->afterStateUpdated(fn($state, $get, $set) => self::checkAvailability($get, $set)),


                Flex::make([
                    DateTimePicker::make('from')
                        ->live(false, 500)
                        ->required()
                        ->label('Vrijeme od')
                        ->seconds(false)
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $reminders = $get('reservationReminders'); // svi repeater items
                            if (!$reminders) return;

                            foreach ($reminders as $index => $item) {
                                $offsetAmount = $item['offset_amount'] ?? null;
                                $offsetUnit = $item['offset_unit'] ?? null;

                                if (!$offsetAmount || !$offsetUnit) continue;


                                $scheduled = self::calculateSendingTime($state, $offsetAmount, $offsetUnit);

                                $set("reservationReminders.{$index}.scheduled_at", $scheduled);
                            }

                            if ($get('service_id') == null) return;

                            $totalMinutes = Service::find($get('service_id'))->duration->minute;
                            $set('to', Carbon::parse($state)->addMinutes($totalMinutes));

                            self::checkAvailability($get, $set);
                        }),

                    DateTimePicker::make('to')
                        ->label('do')
                        ->readOnly()
                        ->required()
                        ->seconds(false),
                ]),

                Textarea::make('note')
                    ->columnSpanFull()
                    ->label('Napomena'),

                Textarea::make('availability_conflicts')
                    ->columnSpanFull()
                    ->label('Conflicts')
                    ->disabled(),

                Tabs::make()
                    ->columnSpanFull()
                    ->contained(false)
                    ->tabs([
                        Tabs\Tab::make('Obavijesti')
                            ->badge(fn(Get $get) => count($get('reservationReminders') ?? []))
                            ->icon(PhosphorIcons::Bell)
                            ->schema([
                                Repeater::make('reservationReminders')
                                    ->columns(7)
                                    ->columnSpanFull()
                                    ->live(true, 500)
                                    ->relationship()
                                    ->reorderable(false)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if (!$state) return;

                                        foreach ($state as $index => $item) {
                                            $offsetAmount = $item['offset_amount'] ?? null;
                                            $offsetUnit = $item['offset_unit'] ?? null;

                                            if (!$offsetAmount || !$offsetUnit) continue;

                                            $scheduled = self::calculateSendingTime($get('from'), $offsetAmount, $offsetUnit);

                                            $set("{$index}.scheduled_at", $scheduled);
                                        }
                                    })
                                    ->maxItems(3)
                                    ->hint('Definirajte do maksimalno 3 podsjetnika prema klijentu.')
                                    ->label('Podsjetnici prema klijentu')
                                    ->addActionLabel('Dodaj podsjetnik')
                                    ->schema([
                                        TextInput::make('offset_amount')
                                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                $scheduledAt = self::calculateSendingTime($get('../../from'), $state, $get('offset_unit'));
                                                $set('scheduled_at', $scheduledAt);
                                            })
                                            ->default(2)
                                            ->columnSpan(1)
                                            ->required()
                                            ->live(true, 500)
                                            ->minValue(1)
                                            ->inputMode('numeric')
                                            ->integer()
                                            ->label('Koliko prije'),

                                        Select::make('offset_unit')
                                            ->default(3)
                                            ->required()
                                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                $scheduledAt = self::calculateSendingTime($get('../../from'), $get('offset_unit'), $state);
                                                $set('scheduled_at', $scheduledAt);
                                            })
                                            ->prefixIcon(PhosphorIcons::Clock)
                                            ->label('Jedinica (minuta, sati, dana)')
                                            ->columnSpan(2)
                                            ->live(true)
                                            ->options([
                                                1 => 'Minuta',
                                                2 => 'Sati',
                                                3 => 'Dana',
                                                4 => 'Tjedana'
                                            ]),
                                        Select::make('channels')
                                            ->minItems(1)
                                            ->columnSpan(2)
                                            ->default(['email'])
                                            ->multiple()
                                            ->label('Preko kanala')
                                            ->options([
                                                'email' => 'Email',
                                                'sms' => 'SMS',
                                            ]),

                                        DateTimePicker::make('scheduled_at')
                                            ->columnSpan(2)
                                            ->required()
                                            ->after('from', true)
                                            ->seconds(false)
                                            ->date()
                                            ->readOnly()
                                            ->validationMessages([
                                                'after' => 'Vrijeme slanja mora biti nakon početka rezervacije.',
                                            ])
                                            ->prefixIcon(PhosphorIcons::Bell)
                                            ->label('Vrijeme slanja')
                                    ])
                            ])
                    ])

            ]);
    }

    public static function checkAvailability($get, $set)
    {
        $start = $get('from');
        $end = $get('to');
        $userId = $get('service_provider_id');
        $roomId = $get('room_id');

        $conflicts = [];

        if ($start && $end) {
            $date = Carbon::parse($start)->format('Y-m-d');
            $start = Carbon::parse($start)->format('H:i');
            $end = Carbon::parse($end)->format('H:i');

            $doctor = User::find($userId);

            if ($doctor && !$doctor->isAvailableAt($date, $start, $end)) {
                $conflicts[] = 'Doktor je zauzet u zadanom periodu.';
            }

            $room = Room::find($roomId);
            if ($room) {
                // Ako model Room koristi HasSchedules trait
                if (!$room->isAvailableAt($date, $start, $end)) {
                    $conflicts[] = 'Soba je zauzeta u zadanom periodu.';
                }
            }
        }

        $set('availability_conflicts', implode(PHP_EOL, $conflicts));
    }

    private static function calculateSendingTime($from, $offsetAmount, $offsetUnit): ?Carbon
    {
        if (!$from || !$offsetAmount || !$offsetUnit) return null;

        $scheduledAt = Carbon::parse($from);

        switch ($offsetUnit) {
            case 1:
                $scheduledAt->subMinutes($offsetAmount);
                break;
            case 2:
                $scheduledAt->subHours($offsetAmount);
                break;
            case 3:
                $scheduledAt->subDays($offsetAmount);
                break;
            case 4:
                $scheduledAt->subWeeks($offsetAmount);
                break;
        }

        return $scheduledAt;
    }
}
