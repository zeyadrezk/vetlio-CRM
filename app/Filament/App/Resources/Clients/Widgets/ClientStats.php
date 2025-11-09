<?php

namespace App\Filament\App\Resources\Clients\Widgets;

use App\Enums\Icons\PhosphorIcons;
use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Livewire\Attributes\Locked;

class ClientStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 3;

    #[Locked]
    public Client $client;

    protected function getStats(): array
    {
        $previousReservation = $this->client->previousReservation;
        $nextReservation = $this->client->nextReservation;
        $totalToPay = $this->client->itemsToPay()->sum('total');

        return [
            Stat::make('Prethodni dolazak', $previousReservation?->date->diffForHumans() ?? ' - ')
                ->description($previousReservation?->date->format('d.m.Y') ?? 'Nema prethodnog dolazaka')
                ->icon(PhosphorIcons::CalendarMinus)
                ->color('info'),

            Stat::make('Sljedeći dolazak', $nextReservation?->date->diffForHumans() ?? ' - ')
                ->description($nextReservation?->date->format('d.m.Y') ?? 'Nema sljedećeg dolazaka')
                ->icon(PhosphorIcons::CalendarPlus)
                ->color('success'),

            Stat::make('Nenaplaćeni iznos', Number::currency($totalToPay / 100))
                ->icon(PhosphorIcons::Money)
                ->description('Ukupno dugovanje klijenta')
                ->color('danger'),
        ];
    }
}
