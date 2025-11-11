<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SequenceGenerator;

class UserObserver
{
    public function creating(User $model): void
    {
        $code = SequenceGenerator::make()
            ->withModel('USER')
            ->withPattern('USER-{{number}}')
            ->withPadding(5)->generate()['sequence'];

        $model->code = $code;
    }
}
