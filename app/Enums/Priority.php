<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Priority: int implements HasLabel, HasColor
{
    case Low = 1;
    case Normal = 2;
    case High = 3;
    case Urgent = 4;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Low => __('enums.priority.low'),
            self::Normal => __('enums.priority.normal'),
            self::High => __('enums.priority.high'),
            self::Urgent => __('enums.priority.urgent'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Normal => 'info',
            self::High => 'warning',
            self::Urgent => 'danger',
        };
    }
}
