<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
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

    public function courseDetails($course)
    {
        $user = Auth::user();
        $course = Course::findOrFail($course); 
        $grade = Grade::where('student_id', $user->id)->where('course_id', $course->id)->firstOrFail(); 

        return view('student.course-details', compact('user', 'course', 'grade'));
    }

    public function courseAttendance($course)
    {
        $user = Auth::user();
        $course = Course::findOrFail($course);
        
        $query = Attendance::where('student_id', $user->id)
                           ->where('course_id', $course->id)
                           ->orderBy('date');
    
        $type = request('type', 'all');
        if ($type === 'lecture' || $type === 'lab') {
            $query->where('type', $type);
        }
    
        $attendances = $query->get();
    
        $allAttendances = Attendance::where('student_id', $user->id)
                                    ->where('course_id', $course->id)
                                    ->get();
        $totalSessions = $allAttendances->count();
        $present = $allAttendances->where('status', 'present')->count();
        $attendanceRate = $totalSessions > 0 ? round(($present / $totalSessions) * 100, 2) : 0;
    
        return view('student.course-attendance', compact('user', 'course', 'attendances', 'attendanceRate', 'totalSessions'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
