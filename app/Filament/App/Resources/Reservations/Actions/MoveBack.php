<?php

namespace App\Filament\App\Resources\Reservations\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class MoveBack extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::ArrowLeft);
        $this->hiddenLabel();
        $this->action(function () {
            $record = $this->getRecord();

            $record->decrementStatus();
        });
    }


}
