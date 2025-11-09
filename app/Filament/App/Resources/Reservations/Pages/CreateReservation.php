<?php

namespace App\Filament\App\Resources\Reservations\Pages;

use App\Filament\App\Resources\Reservations\ReservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;
}
