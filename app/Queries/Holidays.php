<?php

namespace App\Queries;

use App\Models\Holiday;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Holidays
{
    public function forYear(int $year): Collection
    {
        $from = Carbon::create($year, 1, 1);
        $to = Carbon::create($year, 12, 31);

        return $this->forRange($from, $to);
    }

    public function forRange(Carbon $from, Carbon $to): Collection
    {
        $org = auth()->user()->organisation;

        if (!$org || !$org->country_id) {
            return new Collection();
        }

        $defaultLangId = $org->country?->language_id
            ?? $org->load('country:id,default_language_id')->country?->default_language_id;

        $tenantLangId = $org->language_id;

        $enLangId = Cache::rememberForever('lang_en_id', function () {
            return Language::where('iso_639_1', 'en')->value('id');
        });

        $query = Holiday::query()
            ->where('holidays.country_id', $org->country_id)
            ->whereBetween('holidays.date', [$from->toDateString(), $to->toDateString()])
            ->leftJoin('holiday_translations as t_tenant', function ($j) use ($tenantLangId) {
                $j->on('t_tenant.holiday_id', '=', 'holidays.id');
                $tenantLangId
                    ? $j->where('t_tenant.language_id', '=', $tenantLangId)
                    : $j->whereRaw('1=0'); // prevents accidental match
            })
            ->leftJoin('holiday_translations as t_default', function ($j) use ($defaultLangId) {
                $j->on('t_default.holiday_id', '=', 'holidays.id');
                $defaultLangId
                    ? $j->where('t_default.language_id', '=', $defaultLangId)
                    : $j->whereRaw('1=0');
            })
            ->leftJoin('holiday_translations as t_en', function ($j) use ($enLangId) {
                $j->on('t_en.holiday_id', '=', 'holidays.id');
                $enLangId
                    ? $j->where('t_en.language_id', '=', $enLangId)
                    : $j->whereRaw('1=0');
            })
            ->addSelect('holidays.*')
            ->addSelect(DB::raw('COALESCE(t_tenant.name, t_default.name, t_en.name) as translated_name'))
            ->distinct()
            ->orderBy('holidays.date');

        $result = $query->get();

        $result = new Collection(
            $result->unique('id')->values()->all()
        );

        $result->each(function (Holiday $holiday) {
            $holiday->setAttribute('name', $holiday->getAttribute('translated_name'));
            unset($holiday->translated_name);
        });

        return $result;
    }

    public function thisYear(): Collection
    {
        return $this->forYear(now()->year);
    }
}
