<?php

namespace Database\Factories;

use App\Enums\Difficulty;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Quiz::class;


    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "title" => $this->faker->sentence(3),
            "subject" => $this->faker->word(),
            "description" => $this->faker->paragraph(),
            "difficulty" => $this->faker->randomElement(Difficulty::cases()),
            "duration_minutes" => $this->faker->numberBetween(5, 30),
        ];
    }
}
