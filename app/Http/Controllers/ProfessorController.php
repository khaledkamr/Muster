<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    public function index()
    {
        return view('professor.index');
    }

    public function courses()
    {
        $user = Auth::user();
        return view('professor.courses', compact('user'));
    }

    public function assignments()
    {
        $user = Auth::user();
        $assignments = $user->assignments()->with(['course', 'submissions.user'])->get();
        return view('professor.assignments', compact('user', 'assignments'));
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
