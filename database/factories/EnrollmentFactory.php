<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first() ?? User::factory()->student()->create();
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();

        return [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'enrolled_at' => Carbon::now()->startOfYear(),
        ];
    }

    public function forStudentAndCourse(int $studentId, int $courseId, ?Carbon $enrolledAt = null): static
    {
        return $this->state(function (array $attributes) use ($studentId, $courseId, $enrolledAt) {
            return [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'status' => 'enrolled',
                'enrolled_at' => $enrolledAt ?? Carbon::now()->startOfYear(),
            ];
        });
    }
}
