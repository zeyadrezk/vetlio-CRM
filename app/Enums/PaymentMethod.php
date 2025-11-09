<?php

namespace App\Enums;

use App\Enums\Icons\PhosphorIcons;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: int implements HasLabel, HasIcon
{
    case CASH = 1;
    case BANK = 2;
    case CARD = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Gotovina',
            self::BANK => 'Banka',
            self::CARD => 'Kartica',
        };
    }

    public function getIcon(): PhosphorIcons
    {
        return match ($this) {
            self::CASH => PhosphorIcons::Money,
            self::BANK => PhosphorIcons::Bank,
            self::CARD => PhosphorIcons::CreditCard,
        };
    }

}
