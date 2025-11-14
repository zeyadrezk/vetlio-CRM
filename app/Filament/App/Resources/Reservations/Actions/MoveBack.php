<?php

namespace App\Filament\App\Resources\Reservations\Actions;

use App\Filament\App\Resources\Reservations\Pages\ListReservations;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class MoveBack extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::ArrowLeft);
        $this->hiddenLabel();
        $this->visible(function ($livewire, $record) {
            $isWaitingRoom = $livewire instanceof ListReservations;

            return $record->status_id->canMoveBack() && !$record->is_canceled && $isWaitingRoom;
        });
        $this->action(function () {
            $record = $this->getRecord();

            $record->decrementStatus();
        });
    }


}
