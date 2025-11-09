<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $eur = Currency::firstOrCreate(['code' => 'EUR'], [
            'name' => 'Euro',
            'symbol' => '€',
            'minor_unit' => 2,
        ]);

        $hr = Language::firstOrCreate(['iso_639_1' => 'hr'], [
            'iso_639_2' => 'hrv',
            'name_en' => 'Croatian',
            'name_native' => 'Hrvatski',
        ]);

        $en = Language::firstOrCreate(['iso_639_1' => 'en'], [
            'iso_639_2' => 'eng',
            'name_en' => 'English',
            'name_native' => 'English',
        ]);

        Country::firstOrCreate(['iso2' => 'HR'], [
            'iso3' => 'HRV',
            'name_en' => 'Croatia',
            'name_native' => 'Hrvatska',
            'currency_id' => $eur->id,
            'default_language_id' => $hr->id,
            'phone_code' => '385',
        ]);

        Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'name' => 'admin',
            'password' => bcrypt('admin'),
            'email' => 'admin@admin.com',
        ]);

        DB::table('organisations')->insert([
            'uuid' => Str::uuid(),
            'subdomain' => 'org1',
            'name' => 'Test Clinic',
            'language_id' => $hr->id,
            'currency_id' => $eur->id,
            'country_id' => Country::first()->id,
            'address' => 'Test Street',
            'city' => 'Test City',
            'email' => 'admin@test-clinic.com',
            'phone' => '012-345-678'
        ]);

        DB::table('users')->insert([
            'first_name' => 'User 1',
            'last_name' => 'User 1',
            'name' => 'org1',
            'email' => 'admin@org1.com',
            'primary_branch_id' => 1,
            'service_provider' => true,
            'color' => '#ffffff',
            'active' => true,
            'administrator' => true,
            'organisation_id' => 1,
            'password' => bcrypt('org1')
        ]);

        DB::table('branches')->insert([
            'name' => 'Branch 1',
            'active' => true,
            'address' => 'Example street',
            'city' => 'Example Town',
            'postal_code' => '12345',
            'price_list_id' => 1,
            'organisation_id' => 1,
        ]);

        DB::table('branches')->insert([
            'name' => 'Branch 2',
            'active' => true,
            'address' => 'Second Address',
            'city' => 'Example Town',
            'postal_code' => '12345',
            'price_list_id' => 1,
            'organisation_id' => 1,
        ]);

        DB::table('link_user_branches')->insert([
            'user_id' => 1,
            'branch_id' => 1
        ]);

        $this->call(SpeciesAndBreeds::class);
    }
}
