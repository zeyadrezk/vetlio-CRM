<?php

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    protected $model = Organisation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Veterinary Clinic',
            'uuid' => Str::uuid()->toString(),
            'subdomain' => Str::slug($this->faker->unique()->company),
            'logo' => $this->faker->optional()->imageUrl(300, 300, 'animals', true, 'Vetlio'),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'zip_code' => $this->faker->postcode,
            'country_id' => 1,
            'language_id' => 1,
            'currency_id' => 1,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->companyEmail,
            'active' => true,
            'oib' => $this->faker->optional()->numerify('###########'),
            'in_vat_system' => $this->faker->boolean(70),

            // Fiscalisation
            'fiscalization_enabled' => $this->faker->boolean(30),
            'fiscalization_demo' => true,
            'sequence_mark' => $this->faker->optional()->randomLetter(),
            'certificate_path' => null,
            'certificate_password' => null,
            'certificate_valid_to' => null,
            'certificate_details' => null,
        ];
    }

    /**
     * Mark organisation as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn() => ['active' => false]);
    }

    /**
     * Mark organisation as fiscally enabled (production mode).
     */
    public function fiscalized(): static
    {
        return $this->state(fn() => [
            'fiscalization_enabled' => true,
            'fiscalization_demo' => false,
        ]);
    }

    /**
     * Demo organisation — base seed for demo environment.
     */
    public function demo(): static
    {
        return $this->state(fn() => [
            'name' => 'Vetlio Demo Clinic',
            'subdomain' => 'org1',
            'email' => 'demo@vetlio.app',
            'active' => true,
            'fiscalization_enabled' => false,
            'fiscalization_demo' => true,
        ]);
    }
}
