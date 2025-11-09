<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\SequenceGenerator;
use Filament\Facades\Filament;

class PaymentObserver
{
    public function creating(Payment $model): void
    {
        $model->code = SequenceGenerator::make()
            ->withModel('PAYMENT')
            ->withContext([
                'branch' => Filament::getTenant()->branch_mark,
                'year' => now()->year,
            ])
            ->withPattern('UPL-{{number}}/{{branch}}/{{year}}')
            ->generate()['sequence'];
    }
}
