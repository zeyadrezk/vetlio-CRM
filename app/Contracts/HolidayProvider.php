<?php

namespace App\Contracts;

interface HolidayProvider
{
    /**
     * @return array<int, array{
     *   date: string,            // 'YYYY-MM-DD'
     *   observed_date?: string|null,
     *   local_name: string,
     *   english_name: string,
     *   fixed?: bool,
     *   global?: bool,
     *   launch_year?: int|null,
     *   type?: string|null,
     *   provider_uid: string
     * }>
     */
    public function getHolidays(string $countryIso2, int $year): array;
}
