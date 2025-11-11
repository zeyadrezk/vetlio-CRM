<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\Service;
use App\Models\ServiceGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        // Trajanje između 15 i 90 minuta u koracima od 15
        $minutes = $this->faker->randomElement([15, 30, 45, 60, 75, 90]);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        // format HH:MM:SS (npr. 00:15:00)
        $duration = sprintf('%02d:%02d:00', $hours, $mins);

        return [
            'organisation_id' => Organisation::factory(),
            'service_group_id' => ServiceGroup::factory(),
            'name' => $this->faker->randomElement([
                'General Checkup',
                'Vaccination',
                'Dental Cleaning',
                'Surgery Consultation',
                'Blood Test',
                'Ultrasound',
                'X-Ray Examination',
                'Grooming Session',
            ]),
            'code' => strtoupper(Str::random(5)),
            'color' => $this->faker->safeHexColor(),
            'active' => true,
            'duration' => $duration, // e.g. 00:30:00
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['active' => false]);
    }

    public function short(): static
    {
        return $this->state(fn() => ['duration' => '00:15:00']);
    }

    public function long(): static
    {
        return $this->state(fn() => ['duration' => '01:30:00']);
    }

    public function demo(string $name = 'General Checkup'): static
    {
        return $this->state(fn() => [
            'name' => $name,
            'code' => 'GEN01',
            'duration' => '00:30:00',
            'color' => '#3b82f6',
        ]);
    }
}
