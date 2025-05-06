<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function index()
    {
        return view('parent.home');
    }

    public function childGrades($childId)
    {
        $child = User::findOrFail($childId);
        // Fetch grades logic here
        return view('parent.child-grades', compact('child'));
    }

    public function childAssignments($childId)
    {
        $child = User::findOrFail($childId);
        // Fetch assignments logic here
        return view('parent.child-assignments', compact('child'));
    }

    public function childAttendance($childId)
    {
        $child = User::findOrFail($childId);
        // Fetch attendance logic here
        return view('parent.child-attendance', compact('child'));
    }

    public function childProfile($childId)
    {
        $child = User::findOrFail($childId);
        // Fetch profile data logic here
        return view('parent.child-profile', compact('child'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('parent.profile', compact('user'));
    }
}