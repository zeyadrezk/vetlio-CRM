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
            self::Ordered => __('enums.reservation_status.ordered'),
            self::WaitingRoom => __('enums.reservation_status.waiting_room'),
            self::InProcess => __('enums.reservation_status.in_process'),
            self::Completed => __('enums.reservation_status.completed'),
        };
    }
}
