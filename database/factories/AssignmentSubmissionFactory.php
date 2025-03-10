<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Assignment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AssignmentSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'submitted', 'submitted', 'submitted']);
        $submittedAt = $status === 'submitted' ? $this->faker->dateTimeThisYear() : null;
        $score = $status === 'submitted' ? $this->faker->numberBetween(0, 10) : 0;

        return [
            'student_id' => User::factory()->state(['role' => 'student']),
            'assignment_id' => Assignment::factory(),
            'status' => $status,
            'score' => $score,
            'submitted_at' => $submittedAt,
        ];
    }
}
