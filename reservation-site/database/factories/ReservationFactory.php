<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('+1 days', '+1 week');
        $endTime = (clone $startTime)->modify('+1 hour');

        return [
            'user_id' => User::factory(),
            'expert_id' => User::factory(['role' => 'expert']),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
