<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\PriceList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceList>
 */
class PriceListFactory extends Factory
{
    protected $model = PriceList::class;

    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'name' => $this->faker->randomElement([
                'Standard Price List',
                'Surgery Services',
                'Vaccination Packages',
                'Grooming Services',
                'Diagnostics & Lab',
            ]),
            'active' => true,
        ];
    }

    /**
     * State: inactive price list
     */
    public function inactive(): static
    {
        return $this->state(fn() => ['active' => false]);
    }

    /**
     * State: demo price list with predictable naming
     */
    public function demo(string $name = 'Primary Price List'): static
    {
        return $this->state(fn() => [
            'name' => $name,
            'active' => true,
        ]);
    }
}
