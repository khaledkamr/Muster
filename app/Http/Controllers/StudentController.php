<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('student.index', compact('user'));
    }

    public function grades()
    {
        $user = Auth::user();
        $currentYear = 2025;
        $yearOffset = match ($user->year) {
            'freshman' => 0,
            'sophomore' => 1,
            'junior' => 2,
            'senior' => 3,
        };
        $startYear = $currentYear - $yearOffset;
        $currentStudentYear = $yearOffset + 1;

        // Exclude the current semester (Year X Sem 2)
        $enrollments = $user->enrollments()
            ->with('course', 'course.grades')
            ->where(function ($query) use ($startYear, $currentStudentYear) {
                $query->whereRaw("YEAR(enrolled_at) < ?", [$startYear + $currentStudentYear - 1])
                    ->orWhere(function ($q) use ($startYear, $currentStudentYear) {
                        $q->whereRaw("YEAR(enrolled_at) = ?", [$startYear + $currentStudentYear - 1])
                            ->whereHas('course', fn($q) => $q->where('semester', 'first'));
                    });
            })
            ->get();

        return view('student.grades', compact('user', 'enrollments', 'startYear'));
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
        $grades = $user->grades()->with('course')->get();

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
        
        return view('student.profile', compact('user', 'gpa', 'totalCredits', 'maxCredits'));
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
