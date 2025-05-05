<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $professor = Auth::user();
        $courses = $professor->courses()->get();

        // Attendance Summary
        $totalSessions = $courses->sum(function ($course) {
            return $course->attendance->count();
        });
        $presentCount = $courses->sum(function ($course) {
            return $course->attendance->where('status', 'present')->count();
        });
        $attendanceRate = $totalSessions > 0 ? ($presentCount / $totalSessions) * 100 : 0;

        // Assignment Status
        $totalSubmissions = $courses->flatMap(function ($course) {
            return $course->assignments->flatMap->submissions;
        })->count();
        $submittedCount = $courses->flatMap(function ($course) {
            return $course->assignments->flatMap->submissions->where('status', 'submitted');
        })->count();
        $pendingCount = $totalSubmissions - $submittedCount;

        // Student Performance (Average Score)
        $totalScores = $courses->flatMap(function ($course) {
            return $course->assignments->flatMap->submissions->pluck('score');
        })->filter()->avg() ?? 0;

        // Upcoming Events (Placeholder Data)
        $upcomingEvents = [
            ['title' => 'Midterm Exam', 'date' => '2025-05-10'],
            ['title' => 'Class Meeting', 'date' => '2025-05-12'],
        ];

        // Notifications (Placeholder Data)
        $notifications = [
            ['message' => 'Student requested assignment extension', 'time' => '2h ago'],
            ['message' => 'New course material uploaded', 'time' => '1d ago'],
        ];

        return view('professor.index', compact('courses', 'attendanceRate', 'totalSubmissions', 'submittedCount', 'pendingCount', 'totalScores', 'upcomingEvents', 'notifications'));
    }

    public function students($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $students = $course ? $course->enrollments
            ->where('enrolled_at', Carbon::parse('2025-08-01'))
            ->map(function ($enrollment) {
            return $enrollment->student;
            }) : collect();
        return view('professor.students', compact('course', 'courseId', 'students'));
    }

    public function assignments($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $view = request('view', 'assign1');

        $submissions = $course ? $course->assignments
            ->filter(function ($assignment) use ($view) {
                return Carbon::parse($assignment->due_date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01')) 
                    && $assignment->title === ($view === 'assign1' ? 'Assignment 1' : 'Assignment 2');
            })
            ->flatMap(function ($assignment) {
                return $assignment->submissions;
            }) : collect();

        $statusFilter = request('status', 'all');
        $searchQuery = request('search', '');

        $filteredSubmissions = $submissions;

        if ($statusFilter === 'submitted') {
            $filteredSubmissions = $filteredSubmissions->where('status', 'submitted');
        } elseif ($statusFilter === 'pending') {
            $filteredSubmissions = $filteredSubmissions->where('status', 'pending');
        }

        if ($searchQuery) {
            $filteredSubmissions = $filteredSubmissions->filter(function ($submission) use ($searchQuery) {
                return stripos($submission->user->id, $searchQuery) !== false;
            });
        }

        return view('professor.assignments', compact('course', 'courseId', 'filteredSubmissions', 'statusFilter', 'searchQuery'));
    }

    public function attendance($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $view = request('view', 'week1');
        $weeks = [
            'week1' => [
                'start' => Carbon::parse('2025-08-01'),
                'end' => Carbon::parse('2025-08-07'),
            ],
            'week2' => [
                'start' => Carbon::parse('2025-08-08'),
                'end' => Carbon::parse('2025-08-14'),
            ],
            'week3' => [
                'start' => Carbon::parse('2025-08-15'),
                'end' => Carbon::parse('2025-08-21'),
            ],
            'week4' => [
                'start' => Carbon::parse('2025-08-22'),
                'end' => Carbon::parse('2025-08-28'),
            ],
            'week5' => [
                'start' => Carbon::parse('2025-08-29'),
                'end' => Carbon::parse('2025-09-04'),
            ],
            'week6' => [
                'start' => Carbon::parse('2025-09-05'),
                'end' => Carbon::parse('2025-09-11'),
            ],
            'week7' => [
                'start' => Carbon::parse('2025-09-12'),
                'end' => Carbon::parse('2025-09-18'),
            ],
            'week8' => [
                'start' => Carbon::parse('2025-09-19'),
                'end' => Carbon::parse('2025-09-25'),
            ],
            'week9' => [
                'start' => Carbon::parse('2025-09-26'),
                'end' => Carbon::parse('2025-10-02'),
            ],
            'week10' => [
                'start' => Carbon::parse('2025-10-03'),
                'end' => Carbon::parse('2025-10-09'),
            ],
            'week11' => [
                'start' => Carbon::parse('2025-10-10'),
                'end' => Carbon::parse('2025-10-16'),
            ],
        ];

        $attendanceRecords = $course ? $course->attendance
            ->filter(function ($attendance) use ($view, $weeks) {
                return Carbon::parse($attendance->date)->greaterThanOrEqualTo($weeks[$view]['start']) 
                    && Carbon::parse($attendance->date)->lessThanOrEqualTo($weeks[$view]['end']);
            }) : collect();


        // Aggregate attendance data for all weeks
        $weeklyAttendance = [];
        $allAttendanceRecords = $course ? $course->attendance->filter(function ($attendance) {
            return Carbon::parse($attendance->date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01'));
        }) : collect();

        foreach ($weeks as $weekKey => $week) {
            $weekRecords = $allAttendanceRecords->filter(function ($record) use ($week) {
                return Carbon::parse($record->date)->betweenIncluded($week['start'], $week['end']);
            });
            $weeklyAttendance[$weekKey] = [
                'present' => $weekRecords->where('status', 'present')->count(),
                'absent' => $weekRecords->where('status', 'absent')->count(),
                'late' => $weekRecords->where('status', 'late')->count(),
            ];
        }
        // dd($weeklyAttendance);

        $statusFilter = request('status', 'all');
        if($statusFilter === 'present') {
            $attendanceRecords = $attendanceRecords->where('status', 'present');
        } elseif ($statusFilter === 'absent') {
            $attendanceRecords = $attendanceRecords->where('status', 'absent');
        }
        elseif ($statusFilter === 'late') {
            $attendanceRecords = $attendanceRecords->where('status', 'late');
        }

        $typeFilter = request('type', 'all');
        if($typeFilter === 'lecture') {
            $attendanceRecords = $attendanceRecords->where('type', 'lecture');
        } elseif ($typeFilter === 'lab') {
            $attendanceRecords = $attendanceRecords->where('type', 'lab');
        }

        $searchQuery = request('search', '');
        if ($searchQuery) {
            $attendanceRecords = $attendanceRecords->filter(function ($attendance) use ($searchQuery) {
                return stripos($attendance->student->id, $searchQuery) !== false;
            });
        }

        return view('professor.attendance', compact('course', 'courseId', 'attendanceRecords', 'view', 'statusFilter', 'typeFilter', 'searchQuery', 'weeklyAttendance', 'weeks'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('professor.profile', compact('user'));
    }

    public function studentProfile($studentId, $courseId)
    {
        $student = User::findOrFail($studentId);
        $grades = $student->grades()->with('course')->get();

        $gradePoints = [
            'A+' => 4.0, 
            'A'  => 4.8,
            'A-' => 3.7,
            'B+' => 3.3,
            'B'  => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C'  => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D'  => 1.0,
            'D-' => 0.7,
            'F'  => 0.0,
        ];

        $totalPoints = 0;
        $totalCourses = $grades->count();

        foreach ($grades as $grade) {
            $totalPoints += $gradePoints[$grade->grade] ?? 0; 
        }

        $gpa = $totalCourses > 0 ? round($totalPoints / $totalCourses, 2) : 0.00;

        $totalCredits = $grades->sum(function ($grade) {
            return $grade->course->credit_hours ?? 0; 
        });

        $maxCredits = 144;

        return view('professor.student-profile', compact('student', 'courseId', 'gpa', 'totalCredits', 'maxCredits'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
