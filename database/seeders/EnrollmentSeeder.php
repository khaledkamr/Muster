<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        Enrollment::truncate(); // Clear existing enrollments

        $students = User::where('role', 'student')->get();
        $currentYear = 2025; // Current year (March 08, 2025)

        foreach ($students as $student) {
            $enrollments = [];
            $electivesYear3Sem1 = collect();
            $electivesYear3Sem2 = collect();
            $electivesYear4Sem1 = collect();

            switch ($student->year) {
                case 'freshman':
                    $enrollments = Course::where('department', 'General Education')
                        ->whereBetween('code', ['GE101', 'GE106'])
                        ->where('semester', 'first')
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get()
                        ->all();
                    break;

                case 'sophomore':
                    $enrollments = Course::where('department', 'General Education')
                        ->whereBetween('code', ['GE101', 'GE118'])
                        ->whereIn('semester', ['first', 'second'])
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get()
                        ->all();
                    break;

                case 'junior':
                    $geCourses = Course::where('department', 'General Education')
                        ->whereBetween('code', ['GE101', 'GE124'])
                        ->whereIn('semester', ['first', 'second'])
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get();

                    $majorYear3Sem1 = Course::where('department', $student->major)
                        ->whereBetween('code', [$this->getPrefix($student->major) . '301', $this->getPrefix($student->major) . '304'])
                        ->where('semester', 'first')
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get();

                    $electivesYear3Sem1 = Course::where('department', $student->major)
                        ->where('semester', 'first')
                        ->where('type', 'elective')
                        ->orderBy('code')
                        ->limit(2)
                        ->get();

                    $enrollments = array_merge(
                        $geCourses->all(),
                        $majorYear3Sem1->all(),
                        $electivesYear3Sem1->all()
                    );
                    break;

                case 'senior':
                    $geCourses = Course::where('department', 'General Education')
                        ->whereBetween('code', ['GE101', 'GE124'])
                        ->whereIn('semester', ['first', 'second'])
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get();

                    $majorYear3Sem1 = Course::where('department', $student->major)
                        ->whereBetween('code', [$this->getPrefix($student->major) . '301', $this->getPrefix($student->major) . '304'])
                        ->where('semester', 'first')
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get();

                    $electivesYear3Sem1 = Course::where('department', $student->major)
                        ->where('semester', 'first')
                        ->where('type', 'elective')
                        ->orderBy('code')
                        ->limit(2)
                        ->get();

                    $majorYear3Sem2 = Course::where('department', $student->major)
                        ->whereBetween('code', [$this->getPrefix($student->major) . '305', $this->getPrefix($student->major) . '308'])
                        ->where('semester', 'second')
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get();

                    $electivesYear3Sem2 = Course::where('department', $student->major)
                        ->where('semester', 'second')
                        ->where('type', 'elective')
                        ->orderBy('code')
                        ->limit(2)
                        ->get();

                    $majorYear4Sem1 = Course::where('department', $student->major)
                        ->whereBetween('code', [$this->getPrefix($student->major) . '309', $this->getPrefix($student->major) . '312'])
                        ->where('semester', 'first')
                        ->where('type', 'compulsory')
                        ->orderBy('code')
                        ->get();

                    $electivesYear4Sem1 = Course::where('department', $student->major)
                        ->where('semester', 'first')
                        ->where('type', 'elective')
                        ->orderBy('code')
                        ->limit(2)
                        ->get();

                    $enrollments = array_merge(
                        $geCourses->all(),
                        $majorYear3Sem1->all(),
                        $electivesYear3Sem1->all(),
                        $majorYear3Sem2->all(),
                        $electivesYear3Sem2->all(),
                        $majorYear4Sem1->all(),
                        $electivesYear4Sem1->all()
                    );
                    break;
            }

            $uniqueCourseIds = [];
            foreach ($enrollments as $course) {
                if (!in_array($course->id, $uniqueCourseIds)) {
                    $uniqueCourseIds[] = $course->id;
                    $enrolledAt = $this->getEnrollmentDate($course, $student->year, $currentYear);
                    Enrollment::factory()->forStudentAndCourse($student->id, $course->id, $enrolledAt)->create();
                }
            }
        }
    }

    private function getPrefix(string $department): string
    {
        return match ($department) {
            'General Education' => 'GE',
            'Computer Science' => 'CS',
            'Artificial Intelligence' => 'AI',
            'Information System' => 'IS',
            default => throw new \Exception("Unknown department: $department"),
        };
    }

    private function getEnrollmentDate(Course $course, string $studentYear, int $currentYear): Carbon
    {
        $courseCode = (int) preg_replace('/[^0-9]/', '', $course->code); // Extract numeric part (e.g., 301 from ISE301)
        $yearOffset = match ($studentYear) {
            'freshman' => 0,
            'sophomore' => 1,
            'junior' => 2,
            'senior' => 3,
        };
        $startYear = $currentYear - $yearOffset;

        if ($course->type === 'compulsory') {
            if ($courseCode >= 101 && $courseCode <= 124) {
                $courseYear = ceil(($courseCode - 100) / 12); // GE: Year 1 or 2
            } elseif ($courseCode >= 301 && $courseCode <= 304) {
                $courseYear = 3; // Year 3, Sem 1
            } elseif ($courseCode >= 305 && $courseCode <= 308) {
                $courseYear = 3; // Year 3, Sem 2
            } elseif ($courseCode >= 309 && $courseCode <= 312) {
                $courseYear = 4; // Year 4, Sem 1
            } else {
                throw new \Exception("Unknown compulsory course code: $course->code");
            }
        } else { // Elective
            if ($studentYear === 'junior') {
                $courseYear = 3; // Only Year 3, Sem 1 for junior
            } else { // Senior
                $courseYear = match (true) {
                    $courseCode >= 301 && $courseCode <= 302 => 3, // Year 3, Sem 1
                    $courseCode >= 303 && $courseCode <= 304 => 3, // Year 3, Sem 2
                    $courseCode >= 305 && $courseCode <= 306 => 4, // Year 4, Sem 1
                    default => throw new \Exception("Unknown elective course code: $course->code"),
                };
            }
        }

        $semesterOffset = $course->semester === 'first' ? 0 : 6;
        $enrollmentYear = $startYear + ($courseYear - 1);
        return Carbon::create($enrollmentYear, $semesterOffset ? 8 : 1, 1); // Jan 1 or Aug 1
    }
}