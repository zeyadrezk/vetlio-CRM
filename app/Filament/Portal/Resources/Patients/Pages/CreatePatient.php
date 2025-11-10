<?php

namespace App\Filament\Portal\Resources\Patients\Pages;

use App\Filament\Portal\Resources\Patients\PatientResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
}
