<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class PatientMedicalDocuments extends ManageRelatedRecords
{
    protected static string $resource = PatientResource::class;

    protected static string $relationship = 'medicalDocuments';

    protected static ?string $relatedResource = MedicalDocumentResource::class;

    protected static ?string $navigationLabel = 'Nalazi';

    protected static ?string $title = 'Nalazi';

    protected static string|UnitEnum|null $navigationGroup = 'Medicinska dokumentacija';

    public function getSubheading(): string|Htmlable|null
    {
        return $this->getRecord()->description;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
