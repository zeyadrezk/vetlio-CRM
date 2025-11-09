<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Language;
use App\Models\Organisation;
use App\Services\Holidays\HolidaySyncService;
use Illuminate\Console\Command;

class SyncHolidays extends Command
{
    protected $signature = 'holidays:sync
        {--org= : Organisation ID (ako izostaviš, ide za sve orgove ili koristi --country/--languages)}
        {--country= : ISO2 države (npr. HR)}
        {--languages= : Popis jezika (iso_639_1) odvojenih zarezom, npr: hr,en}';

    protected $description = 'Sinkronizira državne blagdane po zemlji i jezicima za tekuću (i opcionalno iduću) godinu.';

    public function handle(HolidaySyncService $service): int
    {
        $now = now();
        $years = [$now->year];
        if ($now->month >= 7) {
            $years[] = $now->year + 1;
        }

        if ($orgId = $this->option('org')) {
            $org = Organisation::query()->findOrFail($orgId);
            $country = Country::query()->findOrFail($org->country_id);
            $tenantLang = Language::query()->findOrFail($org->language_id);

            $langs = collect([$tenantLang, $country->defaultLanguage, Language::where('iso_639_1', 'en')->first()])
                ->filter()
                ->unique('id')
                ->values()
                ->all();

            $this->info("Sync holidays for org={$org->id} country={$country->iso2} years=[" . implode(',', $years) . "]");
            $service->sync($country, $langs, $years);
            $this->info('Done.');
            return self::SUCCESS;
        }

        if ($iso2 = $this->option('country')) {
            $country = Country::where('iso2', strtoupper($iso2))->firstOrFail();

            $langs = collect(explode(',', (string)$this->option('languages')))
                ->map(fn($c) => strtolower(trim($c)))
                ->filter()
                ->map(fn($code) => Language::where('iso_639_1', $code)->first())
                ->filter()
                ->whenEmpty(fn($c) => $c->push(Language::where('iso_639_1', 'en')->first())) // fallback en
                ->unique('id')
                ->values()
                ->all();

            $this->info("Sync holidays for country={$country->iso2} years=[" . implode(',', $years) . "]");
            $service->sync($country, $langs, $years);
            $this->info('Done.');
            return self::SUCCESS;
        }

        $this->info('Sync holidays for ALL organisations...');
        $orgs = Organisation::query()->whereNotNull('country_id')->whereNotNull('language_id')->get();
        foreach ($orgs as $org) {
            $country = Country::find($org->country_id);
            $tenantLang = Language::find($org->language_id);
            if (!$country || !$tenantLang) {
                $this->warn("Skipping org {$org->id} (country/lang missing)");
                continue;
            }
            $langs = collect([$tenantLang, $country->defaultLanguage, Language::where('iso_639_1', 'en')->first()])
                ->filter()
                ->unique('id')
                ->values()
                ->all();

            $this->line(" - org={$org->id} country={$country->iso2}");
            $service->sync($country, $langs, $years);
        }
        $this->info('Done.');
        return self::SUCCESS;
    }
}
