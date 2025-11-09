<?php

use Illuminate\Support\Carbon;

if (!function_exists('addZapPeriod')) {

    function addZapPeriod($availability, $from, $to)
    {
        if ($from instanceof Carbon) {
            $from = $from->format('H:i');
        }

        if ($to instanceof Carbon) {
            $to = $to->format('H:i');
        }

        if (is_string($from) && strlen($from) > 5) {
            $from = Carbon::parse($from)->format('H:i');
        }

        if (is_string($to) && strlen($to) > 5) {
            $to = Carbon::parse($to)->format('H:i');
        }

        return $availability->addPeriod($from, $to);
    }
}
