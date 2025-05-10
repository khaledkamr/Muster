<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\Assignment_submission;
use App\Models\Grade;
use Carbon\Carbon;

class CurrentSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $currentYear = 2025;

        foreach ($students as $student) {
            $yearOffset = match ($student->year) {
                'freshman' => 0,
                'sophomore' => 1,
                'junior' => 2,
                'senior' => 3,
            };
            $startYear = $currentYear - $yearOffset;
            $currentStudentYear = $yearOffset + 1;

            // Add Semester 2 of the current year
            $courses = $this->getCoursesForYearAndSemester($student, $currentStudentYear, 'second');
            $enrollmentYear = $startYear + $currentStudentYear - 1;

            foreach ($courses as $course) {
                $enrolledAt = Carbon::create($enrollmentYear, 8, 1);
                $enrollment = Enrollment::updateOrCreate(
                    ['student_id' => $student->id, 'course_id' => $course->id],
                    ['status' => 'enrolled', 'enrolled_at' => $enrolledAt]
                );

                // Partial Attendance: Aug 1 to Oct 31 (10 weeks)
                $startDate = Carbon::create($enrollmentYear, 8, 1)->addDays(rand(0, 5));
                foreach (['lecture', 'lab'] as $type) {
                    $currentDate = $startDate->copy();
                    for ($i = 0; $i < 10; $i++) {
                        $random = rand(1, 100);
                        $status = $random <= 70 ? 'present' : ($random <= 75 ? 'late' : 'absent');
                        Attendance::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'date' => $currentDate->toDateString(),
                                'type' => $type,
                            ],
                            ['status' => $status]
                        );
                        $currentDate->addDays(7);
                    }
                }

                // Partial Assignments: 2 assignments
                for ($i = 1; $i <= 2; $i++) {
                    $createdAt = Carbon::create($enrollmentYear, 8 + $i, 1);
                    $dueDate = $createdAt->copy()->addDays(10);
                    $assignment = Assignment::updateOrCreate(
                        ['course_id' => $course->id, 'title' => "Assignment $i"],
                        [
                            'description' => "Assignment $i for {$course->name}",
                            'professor_id' => $course->professor_id,
                            'created_at' => $createdAt,
                            'due_date' => $dueDate,
                        ]
                    );

                    $status = rand(1, 100) <= 80 ? 'submitted' : 'pending';
                    $score = $status === 'submitted' ? rand(0, 10) : 0;
                    Assignment_submission::updateOrCreate(
                        ['student_id' => $student->id, 'assignment_id' => $assignment->id],
                        [
                            'status' => $status,
                            'score' => $score,
                            'submitted_at' => $status === 'submitted' ? $createdAt->copy()->addDays(rand(1, 9)) : null,
                        ]
                    );
                }

                // Partial Grades: Quiz 1 and Assignments only
                $quiz1 = rand(3, 10);
                $midterm = rand(0, 30);
                $assignmentsTotal = Assignment_submission::where('student_id', $student->id)
                    ->whereHas('assignment', fn($q) => $q->where('course_id', $course->id))
                    ->sum('score');
                $assignmentsTotal = min($assignmentsTotal, 30);

                Grade::updateOrCreate(
                    ['student_id' => $student->id, 'course_id' => $course->id],
                    [
                        'quiz1' => $quiz1,
                        'quiz2' => null,
                        'midterm' => $midterm,
                        'project' => null,
                        'assignments' => $assignmentsTotal,
                        'final' => null,
                        'total' => null,
                        'grade' => null,
                        'status' => null,
                    ]
                );
            }
        }
    }

    private function getCoursesForYearAndSemester(User $student, int $year, string $semester): array
    {
        $majorPrefix = match ($student->major) {
            'Computer Science' => 'CS',
            'Artificial Intelligence' => 'AI',
            'Information System' => 'IS',
            default => 'GE',
        };

        if ($year <= 2) {
            return Course::where('department', 'General Education')
                ->where('semester', $semester)
                ->where('type', 'compulsory')
                ->whereBetween('code', ['GE' . (($year - 1) * 12 + 101), 'GE' . ($year * 12 + 100)])
                ->orderBy('code')
                ->get()
                ->all();
        } elseif ($year == 3) {
            $compulsory = Course::where('department', $student->major)
                ->where('semester', $semester)
                ->where('type', 'compulsory')
                ->whereBetween('code', [$majorPrefix . '305', $majorPrefix . '308'])
                ->orderBy('code')
                ->get();
            $electives = Course::where('department', $student->major)
                ->where('semester', $semester)
                ->where('type', 'elective')
                ->orderBy('code')
                ->limit(1)
                ->get();
            return array_merge($compulsory->all(), $electives->all());
        } elseif ($year == 4) {
            $compulsory = Course::where('department', $student->major)
                ->where('semester', $semester)
                ->where('type', 'compulsory')
                ->whereBetween('code', [$majorPrefix . '313', $majorPrefix . '316']) // Adjust range if needed
                ->orderBy('code')
                ->get();
            $electives = Course::where('department', $student->major)
                ->where('semester', $semester)
                ->where('type', 'elective')
                ->orderBy('code')
                ->limit(1)
                ->get();
            return array_merge($compulsory->all(), $electives->all());
        }
        return [];
    }
}
