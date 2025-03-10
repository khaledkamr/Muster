<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Assignment_submission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assignment::truncate();
        // Assignment_submission::truncate();

        $courses = Course::with('enrollments')->get();
        $currentYear = 2025;

        foreach ($courses as $course) {
            $earliestEnrollment = $course->enrollments->min('enrolled_at');
            if (!$earliestEnrollment) continue; 

            $startYear = (int) $earliestEnrollment->format('Y');
            $yearsOffered = range($startYear, $currentYear);

            foreach ($yearsOffered as $year) {
                $semesterStart = $course->semester === 'first' ? Carbon::create($year, 1, 1) : Carbon::create($year, 8, 1);

                for ($i = 1; $i <= 3; $i++) {
                    $createdAt = $semesterStart->copy()->addMonths($i);
                    $dueDate = $createdAt->copy()->addDays(10);

                    $assignment = Assignment::create([
                        'title' => "Assignment $i",
                        'description' => $this->faker()->sentence(),
                        'course_id' => $course->id,
                        'professor_id' => $course->professor_id,
                        'created_at' => $createdAt,
                        'due_date' => $dueDate,
                    ]);

                    $enrollments = Enrollment::where('course_id', $course->id)
                        ->whereYear('enrolled_at', $year)
                        ->get();

                    foreach ($enrollments as $enrollment) {
                        $status = $this->faker()->randomElement(['pending', 'submitted', 'submitted', 'submitted']); 
                        $submittedAt = $status === 'submitted'
                            ? $this->faker()->dateTimeBetween($assignment->created_at, $assignment->due_date)
                            : null;
                        $score = $status === 'submitted' ? $this->faker()->numberBetween(0, 10) : 0;

                        Assignment_submission::create([
                            'student_id' => $enrollment->student_id,
                            'assignment_id' => $assignment->id,
                            'status' => $status,
                            'score' => $score,
                            'submitted_at' => $submittedAt,
                        ]);
                    }
                }
            }
        }
    }

    private function faker()
    {
        return \Faker\Factory::create();
    }
}
