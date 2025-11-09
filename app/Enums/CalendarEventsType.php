<?php

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum CalendarEventsType: int implements HasLabel, HasColor, HasIcon
{
    case All = 1;
    case Reservations = 2;
    case WorkPeriods = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::All => 'Sve',
            self::Reservations => 'Rezervacije',
            self::WorkPeriods => 'Termini',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::All => 'warning',
            self::Reservations => 'warning',
            self::WorkPeriods => 'warning',
        };
    }

    public function getIcon(): \BackedEnum|string|null
    {
        return match ($this) {
            self::All => Heroicon::InformationCircle,
            self::Reservations => PhosphorIcons::CalendarPlus,
            self::WorkPeriods => PhosphorIcons::ClockAfternoon
        };
    }
}
