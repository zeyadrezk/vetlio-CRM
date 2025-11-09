<?php

namespace App\Filament\App\Clusters\Setup;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SetupCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog;

    protected static ?string $title = 'Šifrarnik';

    protected static ?string $navigationLabel = 'Šifrarnik';

    protected static ?int $navigationSort = 99;
    
    public static function canAccess(): bool
    {
        return auth()->user()->administrator;
    }
}
