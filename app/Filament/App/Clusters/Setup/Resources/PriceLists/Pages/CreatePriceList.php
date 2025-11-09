<?php

namespace App\Filament\App\Clusters\Setup\Resources\PriceLists\Pages;

use App\Filament\App\Clusters\Setup\Resources\PriceLists\PriceListResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePriceList extends CreateRecord
{
    protected static string $resource = PriceListResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
