<?php

namespace App\Observers;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Zap\Facades\Zap;

class ReservationObserver
{
    public function creating(Reservation $reservation): void
    {
        $reservation->status_id = ReservationStatus::Ordered->value;
        $reservation->date = $reservation->from;
        $reservation->uuid = Str::orderedUuid();

        if ($reservation->branch_id == null) {
            $reservation->branch_id = Filament::getTenant()->id;
        }
    }

    public function created(Reservation $reservation): void
    {
        $this->scheduleResources($reservation);
    }

    //Reservation is changeing, delete schedules
    public function updating(Reservation $reservation): void
    {
        $this->deleteScheduledResources($reservation);
    }

    //After updates, create new schedules
    public function updated(Reservation $reservation): void
    {
        $this->scheduleResources($reservation);
    }

    public function deleted(Reservation $reservation): void
    {
        $this->deleteScheduledResources($reservation);
        $this->deleteReminders($reservation);
    }

    private function deleteScheduledResources(Reservation $reservation): void
    {
        $reservation->room
            ->schedules()
            ->whereJsonContains('metadata->reservation_id', $reservation->id)
            ->delete();
        $reservation->serviceProvider
            ->schedules()
            ->whereJsonContains('metadata->reservation_id', $reservation->id)
            ->delete();
    }

    private function scheduleResources(Reservation $reservation): void
    {
        $user = Zap::for($reservation->serviceProvider)
            ->appointment()
            ->named('Doctor Appointment')
            ->description('Annual checkup')
            ->withMetadata([
                'reservation_id' => $reservation->id,
            ])
            ->from($reservation->date->format('Y-m-d'));

        addZapPeriod($user, $reservation->from, $reservation->to);
        $user->save();

        $room = Zap::for($reservation->room)
            ->appointment()
            ->named('Room Appointment')
            ->description('Annual checkup')
            ->withMetadata([
                'reservation_id' => $reservation->id,
            ])
            ->from($reservation->date->format('Y-m-d'));

        addZapPeriod($room, $reservation->from, $reservation->to);
        $room->save();
    }

    private function deleteReminders(Reservation $reservation): void
    {
        $reservation->reservationReminders()->delete();
    }

}
