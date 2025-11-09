<?php

namespace App\Filament\App\Clusters\Setup\Resources\PriceLists\Pages;

use App\Filament\App\Clusters\Setup\Resources\PriceLists\PriceListResource;
use App\Filament\Exports\PriceListExporter;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPriceList extends ViewRecord
{
    protected static string $resource = PriceListResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
