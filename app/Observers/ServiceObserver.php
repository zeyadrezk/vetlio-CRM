<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\SequenceGenerator;

class ServiceObserver
{
    public function creating(Service $model): void
    {
        $code = SequenceGenerator::make()
            ->withModel('SERVICE')
            ->withPattern('USL-{{number}}')
            ->withPadding(5)->generate()['sequence'];;

        $model->code = $code;
    }


}
