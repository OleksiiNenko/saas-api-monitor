<?php

namespace Database\Factories;

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Monitor>
     */
    protected $model = Monitor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'url' => fake()->url(),
            'method' => fake()->randomElement(['GET', 'HEAD', 'POST']),
            'expected_status' => 200,
            'interval_seconds' => fake()->randomElement([60, 300, 900]),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the monitor is paused.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
