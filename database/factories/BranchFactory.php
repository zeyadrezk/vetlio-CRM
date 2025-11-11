<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'name' => $this->faker->city . ' Clinic',
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'active' => true,
            'branch_mark' => strtoupper(Str::random(3)),
            'price_list_id' => null, // Bit će dodano kasnije nakon kreiranja price listova
        ];
    }

    /**
     * State: inactive branch
     */
    public function inactive(): static
    {
        return $this->state(fn() => ['active' => false]);
    }

    /**
     * State: demo branch with predictable data
     */
    public function demo(string $city = 'Zagreb'): static
    {
        return $this->state(fn() => [
            'name' => "{$city} Veterinary Clinic",
            'city' => $city,
            'active' => true,
        ]);
    }
}
