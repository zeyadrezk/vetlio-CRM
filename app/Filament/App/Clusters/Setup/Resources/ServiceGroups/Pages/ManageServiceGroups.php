<?php

namespace App\Filament\App\Clusters\Setup\Resources\ServiceGroups\Pages;

use App\Filament\App\Clusters\Setup\Resources\ServiceGroups\ServiceGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceGroups extends ManageRecords
{
    protected static string $resource = ServiceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
