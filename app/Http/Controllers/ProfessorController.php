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
        $courseId = $course->id;
        $students = $course ? $course->enrollments->map(function ($enrollment) {
            return $enrollment->student;
        }) : collect();
        // dd($course, $students);
        return view('professor.students', compact('course', 'courseId', 'students'));
    }

    public function assignments($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $courseId = $course->id;

        return view('professor.assignments', compact('course', 'courseId'));
    }

    public function attendance($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $courseId = $course->id;

        return view('professor.attendance', compact('course', 'courseId'));
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
