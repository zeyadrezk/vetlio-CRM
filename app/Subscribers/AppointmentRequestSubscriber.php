<?php

namespace App\Subscribers;

use App\Events\AppointmentRequestApproved;
use App\Events\AppointmentRequestDenied;
use Illuminate\Events\Dispatcher;

class AppointmentRequestSubscriber
{
    public function subscribe(Dispatcher $events): array
    {
        return [
            AppointmentRequestApproved::class => 'handleRequestApproved',
            AppointmentRequestDenied::class => 'handleRequestDenied',
        ];
    }

    public function handleRequestApproved(AppointmentRequestApproved $event): void
    {
        dd("handle event approved..");
    }

    public function handleRequestDenied(AppointmentRequestDenied $event): void
    {
        dd("handle event denied..");
    }
}
