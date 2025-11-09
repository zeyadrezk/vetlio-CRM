<?php

namespace App\Services;

use App\Models\User;
use App\Models\Room;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailableSlotsService
{
    protected string $workdayStart = '08:00';
    protected string $workdayEnd   = '20:00';

    /**
     * Dohvati sve slotove (od–do) s info je li slobodan za oba resursa.
     *
     * @param  User|null  $user
     * @param  Room|null  $room
     * @param  Service    $service
     * @param  Carbon     $date
     * @param  int|null   $bufferMinutes
     * @return \Illuminate\Support\Collection
     */
    public function getSlots(?User $user, ?Room $room, Service $service, Carbon $date, ?int $bufferMinutes = null): Collection
    {
        $dateStr = $date->toDateString();
        $duration = (int) $service->duration->minute;

        // 1️⃣ Dohvati dostupne slotove iz ZAP-a za usera
        $userSlots = $user
            ? collect($user->getAvailableSlots(
                date: $dateStr,
                dayStart: $this->workdayStart,
                dayEnd: $this->workdayEnd,
                slotDuration: $duration,
            ))->map(fn ($s) => [
                'from' => $s['start_time'],
                'to'   => $s['end_time'],
                'is_available' => $s['is_available'],
            ])
            : $this->generateSlots($dateStr, $duration);

        // 2️⃣ Dohvati dostupne slotove iz ZAP-a za sobu
        $roomSlots = $room
            ? collect($room->getAvailableSlots(
                date: $dateStr,
                dayStart: $this->workdayStart,
                dayEnd: $this->workdayEnd,
                slotDuration: $duration,
               // bufferMinutes: $bufferMinutes
            ))->map(fn ($s) => [
                'from' => $s['start_time'],
                'to'   => $s['end_time'],
                'is_available' => $s['is_available'],
            ])
            : $this->generateSlots($dateStr, $duration);

        // 3️⃣ Ako imamo oba — uzmi presjek po vremenu i kombiniraj dostupnost
        $merged = $this->mergeSlots($userSlots, $roomSlots);

        return collect($merged)->map(fn ($s) => [
            'from' => $s['from'],
            'to' => $s['to'],
            'is_available' => $s['is_available'],
        ]);
    }

    /**
     * Spoji slotove usera i sobe (presjek po from–to)
     */
    protected function mergeSlots(Collection $userSlots, Collection $roomSlots): Collection
    {
        return $userSlots->map(function ($slot) use ($roomSlots) {
            $matching = $roomSlots->first(
                fn ($r) => $r['from'] === $slot['from'] && $r['to'] === $slot['to']
            );

            if ($matching) {
                $slot['is_available'] = $slot['is_available'] && $matching['is_available'];
            }

            return $slot;
        });
    }

    /**
     * Generiraj slotove bez provjere dostupnosti (ako nije zadan resurs)
     */
    protected function generateSlots(string $date, int $slotDuration): Collection
    {
        $slots = collect();
        $start = Carbon::parse("$date $this->workdayStart");
        $end   = Carbon::parse("$date $this->workdayEnd");

        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes($slotDuration);
            if ($slotEnd->gt($end)) break;

            $slots->push([
                'from' => $start->format('H:i'),
                'to' => $slotEnd->format('H:i'),
                'is_available' => true,
            ]);

            $start->addMinutes($slotDuration);
        }

        return $slots;
    }
}
