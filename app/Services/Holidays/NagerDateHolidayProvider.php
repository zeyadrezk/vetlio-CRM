<?php

namespace App\Services\Holidays;

use App\Contracts\HolidayProvider;
use Illuminate\Support\Facades\Http;

class NagerDateHolidayProvider implements HolidayProvider
{
    public function getHolidays(string $countryIso2, int $year): array
    {
        $url = "https://date.nager.at/api/v3/PublicHolidays/{$year}/{$countryIso2}";
        $resp = Http::timeout(20)->get($url)->throw()->json();

        $out = [];
        foreach ($resp as $item) {
            // Nager: date, localName, name, countryCode, fixed, global, counties, launchYear, types
            $english = (string)($item['name'] ?? '');
            $local = (string)($item['localName'] ?? $english);
            $date = (string)$item['date']; // "YYYY-MM-DD"
            $fixed = (bool)($item['fixed'] ?? false);
            $global = (bool)($item['global'] ?? true);
            $launch = $item['launchYear'] ?? null;
            $type = is_array($item['types'] ?? null) ? implode(',', $item['types']) : ($item['type'] ?? null);

            // stabilan uid (hash kombinacije zemlje+datum+engleskog naziva)
            $uid = hash('sha256', "{$countryIso2}|{$date}|{$english}");

            $out[] = [
                'date' => $date,
                'observed_date' => null,
                'local_name' => $local,
                'english_name' => $english,
                'fixed' => $fixed,
                'global' => $global,
                'launch_year' => $launch,
                'type' => $type,
                'provider_uid' => $uid,
            ];
        }
        return $out;
    }
}
