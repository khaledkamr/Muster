<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('professor.index', compact('user'));
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

        return view('professor.attendance', compact('course', 'courseId'));
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
