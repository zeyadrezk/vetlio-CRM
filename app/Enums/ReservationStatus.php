<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReservationStatus: int implements HasLabel
{
    case Ordered = 1;
    case WaitingRoom = 2;
    case InProcess = 3;
    case Completed = 4;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Ordered => 'Naručen',
            self::WaitingRoom => 'Čekaonica',
            self::InProcess => 'U obradi',
            self::Completed => 'Završen',
        };
    }
}
