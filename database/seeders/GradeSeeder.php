<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Assignment_submission;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            $enrollments = $student->enrollments()->with('course')->get();

            foreach ($enrollments as $enrollment) {
                $course = $enrollment->course;

                $assignmentsTotal = Assignment_submission::where('student_id', $student->id)
                    ->whereHas('assignment', function ($query) use ($course) {
                        $query->where('course_id', $course->id);
                    })
                    ->sum('score');

                $assignmentsTotal = min($assignmentsTotal, 30);

                $quiz1 = $this->generateNormalScore(5, 2, 0, 10); 
                $quiz2 = $this->generateNormalScore(5, 2, 0, 10); 
                $midterm = $this->generateNormalScore(19, 5, 0, 30); 
                $project = $this->generateNormalScore(19, 5, 0, 30);
                $final = $this->generateNormalScore(39, 8, 0, 60);  

                $total = $quiz1 + $quiz2 + $midterm + $project + $assignmentsTotal + $final;

                $grade = $this->calculateGrade($total);

                $status = ($grade === 'F') ? 'Fail' : 'Pass';

                // Insert or update grade record
                Grade::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'quiz1' => $quiz1,
                        'quiz2' => $quiz2,
                        'midterm' => $midterm,
                        'project' => $project,
                        'assignments' => $assignmentsTotal,
                        'final' => $final,
                        'total' => $total,
                        'grade' => $grade,
                        'status' => $status,
                    ]
                );
            }
        }
    }

    private function generateNormalScore($mean, $sd, $min, $max)
    {
        // Generate a normally distributed random number using the Box-Muller transform
        $u1 = mt_rand() / mt_getrandmax();
        $u2 = mt_rand() / mt_getrandmax();
        $z = sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);

        // Scale and shift to desired mean and standard deviation
        $score = $mean + $sd * $z;

        // Clamp the score to the min and max bounds
        return max($min, min($max, round($score)));
    }

    private function calculateGrade($total)
    {
        if ($total >= 161.5) return 'A+'; // 95% of 170 = 161.5
        elseif ($total >= 153) return 'A'; // 90% = 153
        elseif ($total >= 144.5) return 'A-'; // 85% = 144.5
        elseif ($total >= 136) return 'B+'; // 80% = 136
        elseif ($total >= 127.5) return 'B'; // 75% = 127.5
        elseif ($total >= 122.4) return 'B-'; // 72% = 122.4
        elseif ($total >= 119) return 'C+'; // 70% = 119
        elseif ($total >= 110.5) return 'C'; // 65% = 110.5
        elseif ($total >= 107.1) return 'C-'; // 63% = 107.1
        elseif ($total >= 102) return 'D+'; // 60% = 102
        elseif ($total >= 93.5) return 'D'; // 55% = 93.5
        elseif ($total >= 85) return 'D-'; // 50% = 85
        else return 'F'; // Below 50%
    }
}
