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

        $courseNames = [
            'Computer Science' => ['Introduction to Programming', 'Object Oriented Programming', 'Data Structures', 'Algorithms', 'Operating Systems', 'Software Engineering'],
            'Artificial Intelligence' => ['Machine Learning I', 'Neural Networks', 'AI Ethics', 'Natural Language Processing', 'Computer Vision', 'Data Science'],
            'Mathematics' => ['Calculus I', 'Linear Algebra', 'Discrete Mathematics', 'Probability and Statistics', 'Differential Equations'],
            'Physics' => ['Mechanics', 'Electromagnetism', 'Quantum Physics', 'Thermodynamics', 'Optics'],
            'Information System' => ['System Analysis', 'Information Security', 'Database Management I', 'Computer Networks I', 'Enterprise Systems'],
        ];
        
        $name = fake()->randomElement($courseNames[$department]);

        $professor = User::where('role', 'professor')->where('department', $department)
            ->inRandomOrder()
            ->first() ?? User::factory()->professor()->create(['department' => $department]);

        return [
            'name' => $name,
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

    public function forCourse(string $department, string $name, string $code, string $type): static
    {
        return $this->state(function (array $attributes) use ($department, $name, $code, $type) {
            $professor = User::where('role', 'professor')
                ->where('department', $department)
                ->inRandomOrder()
                ->first() ?? User::factory()->professor()->create(['department' => $department]);

            return [
                'name' => $name,
                'code' => $code,
                'department' => $department,
                'type' => $type,
                'professor_id' => $professor->id,
            ];
        });
    }
}
