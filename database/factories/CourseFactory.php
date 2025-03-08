<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            'General Education' => 'GE', 
            'Computer Science' => 'CS',
            'Artificial Intelligence' => 'AI',
            'Mathematics' => 'MATH',
            'Physics' => 'PHY',
            'Information System' => 'IS',
        ];

        $department = fake()->randomElement(array_keys($departments));
        $codePrefix = $departments[$department];
        $codeNumber = fake()->unique()->numberBetween(101, 499);
        $code = "$codePrefix$codeNumber";

        $professor = User::where('role', 'professor')
            ->where('department', $department)
            ->inRandomOrder()
            ->first() ?? User::factory()->professor()->create(['department' => $department]);

        return [
            'name' => fake()->words(3, true),
            'code' => $code,
            'description' => fake()->sentence(10),
            'department' => $department,
            'credit_hours' => fake()->randomElement([3, 4]),
            'semester' => fake()->randomElement(['first', 'second', 'both']),
            'type' => fake()->randomElement(['elective', 'compulsory']),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'professor_id' => $professor->id,
        ];
    }

    public function forCourse(string $department, string $name, string $code, string $semester, string $type, string $difficulty): static
    {
        return $this->state(function (array $attributes) use ($department, $name, $code, $semester, $type, $difficulty) {
            $professor = User::where('role', 'professor')
                ->where('department', $department)
                ->inRandomOrder()
                ->first() ?? User::factory()->professor()->create(['department' => $department]);

            return [
                'name' => $name,
                'code' => $code,
                'department' => $department,
                'semester' => $semester,
                'type' => $type,
                'difficulty' => $difficulty,
                'credit_hours' => 3,
                'professor_id' => $professor->id,
            ];
        });
    }
}
