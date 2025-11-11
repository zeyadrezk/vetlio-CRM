<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\Price;
use App\Models\PriceList;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        // Generiraj osnovnu cijenu bez PDV-a
        $base = $this->faker->randomFloat(2, 10, 150); // 10.00 – 150.00 €
        $vat = $this->faker->randomElement([13, 25]);
        $withVat = round($base * (1 + $vat / 100), 2);

        return [
            'organisation_id' => Organisation::factory(),
            'price_list_id' => PriceList::factory(),
            'priceable_id' => null,
            'priceable_type' => null,
            'price' => $base, // normalna decimalna cijena
            'vat_percentage' => $vat,
            'price_with_vat' => $withVat,
            'valid_from_at' => Carbon::now()->subDays(rand(0, 60)),
        ];
    }

    /**
     * Future price
     */
    public function future(): static
    {
        return $this->state(fn () => [
            'valid_from_at' => now()->addDays(rand(1, 30)),
        ]);
    }

    /**
     * Expired price
     */
    public function expired(): static
    {
        return $this->state(fn () => [
            'valid_from_at' => now()->subMonths(rand(2, 6)),
        ]);
    }

    /**
     * Demo predictable price
     */
    public function demo(): static
    {
        return $this->state(fn () => [
            'price' => 40.00,
            'vat_percentage' => 25,
            'price_with_vat' => 50.00,
            'valid_from_at' => now()->subMonth(),
        ]);
    }
}
