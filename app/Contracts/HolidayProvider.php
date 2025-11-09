<?php

namespace App\Contracts;

interface HolidayProvider
{
    /**
     * @return array<int, array{
     *   date: string,            // 'YYYY-MM-DD'
     *   observed_date?: string|null,
     *   local_name: string,      // lokalni naziv iz zemlje
     *   english_name: string,    // engleski naziv
     *   fixed?: bool,
     *   global?: bool,
     *   launch_year?: int|null,
     *   type?: string|null,
     *   provider_uid: string     // stabilni uid (npr. hash)
     * }>
     */
    public function getHolidays(string $countryIso2, int $year): array;
}
