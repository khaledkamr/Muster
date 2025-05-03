<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
        $students = $course ? $course->enrollments->map(function ($enrollment) {
            return $enrollment->student;
        }) : collect();
        // dd($course, $students);
        return view('professor.students', compact('course', 'students'));
    }

    public function courses()
    {
        $user = Auth::user();
        return view('professor.courses', compact('user'));
    }

    public function assignments(Request $request)
    {
        $courseId = $request->query('course_id');
        $course = $courseId ? Course::find($courseId) : null;
        // Fetch assignments for the selected course (example logic)
        $assignments = $course ? $course->assignments : collect();
        return view('professor.assignments', compact('course', 'assignments'));
    }

    public function attendance(Request $request)
    {
        $courseId = $request->query('course_id');
        $course = $courseId ? Course::find($courseId) : null;
        // Fetch attendance for the selected course (example logic)
        $attendance = $course ? $course->attendance : collect();
        return view('professor.attendance', compact('course', 'attendance'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('professor.profile', compact('user'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
