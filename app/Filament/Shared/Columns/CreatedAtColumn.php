<?php

namespace App\Filament\Shared\Columns;

use Filament\Tables\Columns\TextColumn;

class CreatedAtColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('created_at');
        $this->label('Created at');
        $this->dateTime();
        $this->sortable();
        $this->toggleable(isToggledHiddenByDefault: true);
    }

    public static function getDefaultName(): ?string
    {
        return 'created_at';
    }
}
