<?php

namespace App\Filament\App\Resources\Reservations\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class MoveRight extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::ArrowRight);
        $this->hiddenLabel();
        $this->action(function () {
            $record = $this->getRecord();

            $record->incrementStatus();
        });
    }


}
