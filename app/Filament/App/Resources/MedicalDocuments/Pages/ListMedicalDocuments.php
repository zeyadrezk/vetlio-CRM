<?php

namespace App\Filament\App\Resources\MedicalDocuments\Pages;

use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicalDocuments extends ListRecords
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
