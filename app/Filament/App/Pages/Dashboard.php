<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected string $view = 'filament.app.pages.dashboard';

    protected static ?int $navigationSort = -2;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    public function getTitle(): string|Htmlable
    {
        return 'Hi, ' . auth()->user()->first_name;
    }
}
