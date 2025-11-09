<?php

namespace App\Filament\App\Entries;

use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PlaceholderEntry extends TextEntry
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->id(Str::uuid());
        $this->name(Str::uuid());
        $this->hiddenLabel();
        $this->html();
        $this->columnSpanFull();
        $this->state(new HtmlString('<hr class="border-gray-200"/>'));
    }
}
