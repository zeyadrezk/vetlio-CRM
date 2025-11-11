<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Organisation;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'branch_id' => Branch::factory(),
            'name' => $this->faker->randomElement([
                'Exam Room 1',
                'Exam Room 2',
                'Surgery Room',
                'Ultrasound Room',
                'X-Ray Room',
                'Consultation Room',
            ]),
            'code' => strtoupper(Str::random(3)),
            'color' => $this->faker->safeHexColor(),
            'active' => true,
        ];
    }

    /**
     * State: mark room as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn() => ['active' => false]);
    }

    /**
     * State: generate room with predictable naming for demo.
     */
    public function demo(string $name = 'Exam Room 1', string $code = 'RM1'): static
    {
        return $this->state(fn() => [
            'name' => $name,
            'code' => $code,
            'active' => true,
        ]);
    }
}
