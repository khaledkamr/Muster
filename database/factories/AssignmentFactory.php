<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['Assignment 1', 'Assignment 2', 'Assignment 3']),
            'description' => $this->faker->sentence(),
            'course_id' => Course::factory(),
            'professor_id' => function (array $attributes) {
                return Course::find($attributes['course_id'])->professor_id;
            },
            'created_at' => $this->faker->dateTimeThisYear(),
            'due_date' => function (array $attributes) {
                return \Carbon\Carbon::parse($attributes['created_at'])->addDays(10);
            },
        ];
    }
}
