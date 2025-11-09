<?php

namespace App\Services\Holidays;

use App\Contracts\HolidayProvider;
use App\Models\Country;
use App\Models\Holiday;
use App\Models\HolidayTranslation;
use App\Models\Language;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HolidaySyncService
{
    public function __construct(private HolidayProvider $provider) {}

    /**
     * @param Country $country
     * @param array<int, Language> $languages  Jezici za koje želiš prijevode (min. eng i/ili default jezik zemlje)
     * @param int[] $years
     */
    public function sync(Country $country, array $languages, array $years): void
    {
        foreach ($years as $year) {
            $items = $this->provider->getHolidays($country->iso2, $year);
            DB::transaction(function () use ($country, $languages, $items) {
                foreach ($items as $i) {
                    $holiday = Holiday::query()->updateOrCreate(
                        ['country_id' => $country->id, 'provider_uid' => $i['provider_uid']],
                        [
                            'date'          => $i['date'],
                            'observed_date' => Arr::get($i, 'observed_date'),
                            'fixed'         => (bool) $i['fixed'],
                            'global'        => (bool) $i['global'],
                            'launch_year'   => $i['launch_year'] ?? null,
                            'type'          => $i['type'] ?? null,
                        ]
                    );

                    // mapiranje prijevoda:
                    // - ako je jezik = eng => koristi english_name
                    // - ako je jezik = defaultLanguage zemlje => koristi local_name
                    // - ostalo: fallback na english_name (možeš proširiti drugim providerom)
                    foreach ($languages as $lang) {
                        $name = null;
                        if (strtolower($lang->iso_639_1) === 'en') {
                            $name = $i['english_name'];
                        } elseif ($lang->id === $country->default_language_id) {
                            $name = $i['local_name'];
                        } else {
                            $name = $i['english_name']; // fallback
                        }

                        HolidayTranslation::query()->updateOrCreate(
                            ['holiday_id' => $holiday->id, 'language_id' => $lang->id],
                            ['name' => $name]
                        );
                    }
                }
            });
        }
    }
}
