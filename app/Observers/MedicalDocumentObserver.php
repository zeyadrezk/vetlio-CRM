<?php

namespace App\Observers;

use App\Models\MedicalDocument;
use App\Services\SequenceGenerator;
use Filament\Facades\Filament;

class MedicalDocumentObserver
{
    public function creating(MedicalDocument $model): void
    {
        $model->code = SequenceGenerator::make()
            ->withModel('MEDICALDOCUMENT')
            ->withContext([
                'branch' => Filament::getTenant()->branch_mark,
                'year' => now()->year,
            ])
            ->withPattern('MED-{{number}}/{{branch}}/{{year}}')
            ->generate()['sequence'];
    }

}
