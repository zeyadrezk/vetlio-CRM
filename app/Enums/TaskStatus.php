<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum TaskStatus: int implements HasLabel, HasColor, HasIcon
{
    case Created = 1;
    case InProgress = 2;
    case Completed = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Created => 'Kreirane',
            self::InProgress => 'U izradi',
            self::Completed => 'Završen',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Created => 'danger',
            self::InProgress => 'gray',
            self::Completed => 'success',
        };
    }

    public function getIcon(): \BackedEnum|string|null
    {
        return match ($this) {
            self::Created => Heroicon::InformationCircle,
            self::InProgress => Heroicon::Swatch,
            self::Completed => Heroicon::Check
        };
    }
}
