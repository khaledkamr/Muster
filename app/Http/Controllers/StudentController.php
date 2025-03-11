<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('student.index', compact('user'));
    }

    public function courses()
    {
        $user = Auth::user();
        return view('student.courses', compact('user'));
    }

    public function assignments()
    {
        $user = Auth::user();
        $submissions = $user->assignmentSubmissions()->with('assignment.course')->get();
        return view('student.assignments', compact('user', 'submissions'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
