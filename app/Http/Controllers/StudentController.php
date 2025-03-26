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
        $enrollments = $user->enrollments()->with('course.grades')->get();

        // Determine the start year of the student (based on earliest enrollment)
        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : now()->format('Y');

        // Determine the current semester and year
        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second'; // First semester: Jan-Jun, Second: Jul-Dec
        $currentAcademicYear = $currentYear - $startYear + 1;

        // Generate all semesters the student has been enrolled in
        $semesters = [];
        foreach ($enrollments as $enrollment) {
            $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
            $semesterKey = $enrollment->course->semester;
            $semesterLabel = "Year $enrollmentYear - " . ($semesterKey === 'first' ? 'First Semester' : 'Second Semester');
            $semesterValue = "year{$enrollmentYear}-{$semesterKey}";
            $semesters[$semesterValue] = $semesterLabel;
        }

        // Remove duplicates and sort semesters
        $semesters = array_unique($semesters);
        asort($semesters);

        // Add the current semester to the list (for display purposes, but grades will be empty)
        $currentSemesterValue = "year{$currentAcademicYear}-{$currentSemester}";
        $currentSemesterLabel = "Year $currentAcademicYear - " . ($currentSemester === 'first' ? 'First Semester' : 'Second Semester');
        $semesters[$currentSemesterValue] = $currentSemesterLabel;

        return view('student.grades', compact('user', 'enrollments', 'startYear', 'semesters', 'currentSemesterValue'));
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

        if (!$grade) {
            abort(404, 'Grade not found for this course.');
        }

        $maxScores = [
            'quiz1' => 10,
            'quiz2' => 10,
            'midterm' => 30,
            'project' => 30,
            'assignments' => 30, // Combined score for Assignment 1, 2, and 3
            'final' => 60,
        ];

        // Prepare scores for display
        $displayScores = [
            'quiz1' => $grade->quiz1,
            'quiz2' => $grade->quiz2,
            'midterm' => $grade->midterm,
            'project' => $grade->project,
            'assignments' => $grade->assignments, // Combined Assignments score
            'final' => $grade->final,
        ];

        // Max scores for display
        $displayMaxScores = [
            'quiz1' => $maxScores['quiz1'],
            'quiz2' => $maxScores['quiz2'],
            'midterm' => $maxScores['midterm'],
            'project' => $maxScores['project'],
            'assignments' => $maxScores['assignments'],
            'final' => $maxScores['final'],
        ];

        // Calculate total score out of 170
        $totalMaxScore = array_sum($maxScores); // 170
        $totalScore = $grade->quiz1 + $grade->quiz2 + $grade->midterm + $grade->project + $grade->assignments + $grade->final;

        // Calculate percentage
        $percentage = round(($totalScore / $totalMaxScore) * 100);

        return view('student.course-details', compact('course', 'grade', 'displayScores', 'displayMaxScores', 'totalScore', 'totalMaxScore', 'percentage'));
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
