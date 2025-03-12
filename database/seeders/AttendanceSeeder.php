<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
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
                $enrollmentYear = $enrollment->enrolled_at->year;
                $semesterStart = $course->semester === 'first' ? "$enrollmentYear-01-01" : "$enrollmentYear-08-01";

                $startDate = Carbon::parse($semesterStart)->addDays(rand(0, 5));

                foreach (['lecture', 'lab'] as $type) {
                    $currentDate = $startDate->copy();
                    for ($i = 0; $i < 16; $i++) {
                        $random = rand(1, 100);
                        $status = $random <= 70 ? 'present' : ($random <= 75 ? 'late' : 'absent');

                        Attendance::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'date' => $currentDate->toDateString(),
                                'type' => $type,
                            ],
                            [
                                'status' => $status,
                            ]
                        );

                        $currentDate->addDays(7);
                    }
                }
            }
        }
    }
}
