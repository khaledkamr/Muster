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
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if($user->major == 'Computer Science') {
            $major = 'CS';
        } elseif($user->major == 'Artificial Intelligence') {
            $major = 'AI';
        } elseif($user->major == 'Information System') {
            $major = 'IS';
        } else {
            $major = 'GE';
        }

        $enrollments = $user->enrollments()->with('course')->get();

        // Determine the current semester and year
        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second';
        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : $currentYear;
        $currentAcademicYear = $currentYear - $startYear + 1;

        // Get current semester courses
        $currentSemesterCourses = $enrollments
            ->filter(function ($enrollment) use ($currentAcademicYear, $currentSemester, $startYear) {
                $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                return $enrollmentYear === $currentAcademicYear && $enrollment->course->semester === $currentSemester;
            })
            ->pluck('course');

        $grades = $user->grades()->with('course')->get();

        $gradePoints = [
            'A+' => 4.0, 'A' => 4.8, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D' => 1.0, 'D-' => 0.7, 'F' => 0.0,
        ];

        $totalPoints = 0;
        $totalCourses = $grades->count();

        foreach ($grades as $grade) {
            $totalPoints += $gradePoints[$grade->grade] ?? 0; 
        }

        $gpa = $totalCourses > 0 ? round($totalPoints / $totalCourses, 2) : 0.00;
        $gpa_progress = ($gpa / 4.0) * 100;

        $totalCredits = $grades->sum(function ($grade) {
            return $grade->course->credit_hours ?? 0; 
        });

        $maxCredits = 144;
        $credits_progress = ($totalCredits / $maxCredits) * 100;

        $upcomingAssignments = ['Assignment 3',];

        // Define semester date range (assuming first semester: Jan-Jun)
        $semesterStart = Carbon::create($currentYear, $currentSemester === 'first' ? 1 : 8, 1);
        $semesterEnd = Carbon::create($currentYear, $currentSemester === 'first' ? 6 : 11, 30);

        // Get all attendance records for the current semester
        $attendances = Attendance::where('student_id', $user->id)
            ->whereIn('course_id', $currentSemesterCourses->pluck('id'))
            ->whereBetween('date', [$semesterStart, $semesterEnd])
            ->get();

        // Calculate weekly attendance (lectures, labs, or both)
        $weeksInSemester = $semesterStart->diffInWeeks($semesterEnd) + 1;
        $weeklyAttendance = [];
        $totalSessions = 0;
        $presentSessions = 0;

        for ($week = 1; $week <= $weeksInSemester; $week++) {
            $weekStart = $semesterStart->copy()->addWeeks($week - 1)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            $weekAttendances = $attendances->filter(function ($attendance) use ($weekStart, $weekEnd) {
                $attendanceDate = Carbon::parse($attendance->date);
                $isWithinWeek = $attendanceDate->between($weekStart, $weekEnd);
                return $isWithinWeek;
            });

            $sessionsInWeek = $weekAttendances->count();
            $presentInWeek = $weekAttendances->where('status', 'present')->count();
            $weeklyAttendance[$week] = $presentInWeek;

            $totalSessions += $sessionsInWeek;
            $presentSessions += $presentInWeek;
        }

        // Calculate attendance rate
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;

        // Prepare contribution graph data for the selected course
        $contributionData = [];
        $currentDate = $semesterStart->copy();
        
        while ($currentDate <= $semesterEnd) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayAttendances = $attendances->where('date', $currentDate);
            $attended = $dayAttendances->contains('status', 'present');
            $contributionData[$dateStr] = $attended ? 1 : 0; // 1 for attended, 0 for not attended
            $currentDate->addDay();
        }

        return view('student.index', compact(
            'user', 
            'major', 
            'currentSemesterCourses', 
            'gpa', 
            'gpa_progress', 
            'totalCredits', 
            'credits_progress', 
            'maxCredits', 
            'upcomingAssignments',
            'weeklyAttendance',
            'attendanceRate',
            'semesterStart',
            'semesterEnd',
            'contributionData'
        ));
    }

    public function grades()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course.grades')->get();

        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : now()->format('Y');

        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second'; // First semester: Jan-Jun, Second: Jul-Dec
        $currentAcademicYear = $currentYear - $startYear + 1;

        $semesters = [];
        foreach ($enrollments as $enrollment) {
            $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
            $semesterKey = $enrollment->course->semester;
            $semesterLabel = "Year $enrollmentYear - " . ($semesterKey === 'first' ? 'First Semester' : 'Second Semester');
            $semesterValue = "year{$enrollmentYear}-{$semesterKey}";
            $semesters[$semesterValue] = $semesterLabel;
        }

        $semesters = array_unique($semesters);
        asort($semesters);

        $currentSemesterValue = "year{$currentAcademicYear}-{$currentSemester}";
        $currentSemesterLabel = "Year $currentAcademicYear - " . ($currentSemester === 'first' ? 'First Semester' : 'Second Semester');
        $semesters[$currentSemesterValue] = $currentSemesterLabel;

        return view('student.grades', compact('user', 'enrollments', 'startYear', 'semesters', 'currentSemesterValue'));
    }

    public function assignments(Request $request)
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course.assignments.submissions')->get();

        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second'; // First: Jan-Jun, Second: Jul-Dec
        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : $currentYear;
        $currentAcademicYear = $currentYear - $startYear + 1;

        $currentSemesterCourses = $enrollments
            ->filter(function ($enrollment) use ($currentAcademicYear, $currentSemester, $startYear) {
                $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                return $enrollmentYear === $currentAcademicYear && $enrollment->course->semester === $currentSemester;
            })
            ->pluck('course');
        
        $assignments = $currentSemesterCourses->flatMap->assignments;

        $submissions = $assignments->flatMap->submissions->where('student_id', $user->id);

        $totalAssignments = $assignments->count() - 4;
        $submittedAssignments = $submissions->where('status', 'submitted')->count();
        $completionPercentage = $totalAssignments > 0 ? round(($submittedAssignments / $totalAssignments) * 100) : 0;

        $totalScore = $submissions->where('status', 'submitted')->sum('score');
        $maxScorePerAssignment = 10; 
        $maxPossibleScore = $submittedAssignments * $maxScorePerAssignment;
        $scorePercentage = $maxPossibleScore > 0 ? round(($totalScore / $maxPossibleScore) * 100) : 0;

        $postedAssignmentTitles = $assignments->pluck('title')->toArray();
        $allPossibleAssignments = ['Assignment 1', 'Assignment 2', 'Assignment 3']; 
        $upcomingAssignments = ['Assignment 3'];

        $statusFilter = $request->input('status', 'all');
        $filteredSubmissions = $submissions;
        if ($statusFilter === 'submitted') {
            $filteredSubmissions = $submissions->where('status', 'submitted');
        } elseif ($statusFilter === 'pending') {
            $filteredSubmissions = $submissions->where('status', 'pending');
        }

        $searchQuery = $request->input('search', '');
        if ($searchQuery) {
            $filteredSubmissions = $filteredSubmissions->filter(function ($submission) use ($searchQuery) {
                $course = $submission->assignment->course;
                return stripos($course->code, $searchQuery) !== false || stripos($course->name, $searchQuery) !== false;
            });
        }

        return view('student.assignments', compact(
            'submissions',
            'filteredSubmissions',
            'upcomingAssignments',
            'completionPercentage',
            'scorePercentage',
            'statusFilter',
            'searchQuery',
            'currentSemesterCourses'
        ));
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

    public function attendance(Request $request)
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course')->get();

        // Determine the current semester and year
        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second';
        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : $currentYear;
        $currentAcademicYear = $currentYear - $startYear + 1;

        // Define semester date range (assuming first semester: Jan-Jun)
        $semesterStart = Carbon::create($currentYear, $currentSemester === 'first' ? 1 : 8, 1);
        $semesterEnd = Carbon::create($currentYear, $currentSemester === 'first' ? 6 : 11, 30);

        // Get current semester courses
        $currentSemesterCourses = $enrollments
            ->filter(function ($enrollment) use ($currentAcademicYear, $currentSemester, $startYear) {
                $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                return $enrollmentYear === $currentAcademicYear && $enrollment->course->semester === $currentSemester;
            })
            ->pluck('course');

        // Get all attendance records for the current semester
        $attendances = Attendance::where('student_id', $user->id)
            ->whereIn('course_id', $currentSemesterCourses->pluck('id'))
            ->whereBetween('date', [$semesterStart, $semesterEnd])
            ->get();

        // Calculate weekly attendance (lectures, labs, or both)
        $filterType = $request->input('type', 'both'); // Filter: lectures, labs, or both
        $weeksInSemester = $semesterStart->diffInWeeks($semesterEnd) + 1;
        $weeklyAttendance = [];
        $totalSessions = 0;
        $presentSessions = 0;

        for ($week = 1; $week <= $weeksInSemester; $week++) {
            $weekStart = $semesterStart->copy()->addWeeks($week - 1)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            $weekAttendances = $attendances->filter(function ($attendance) use ($weekStart, $weekEnd, $filterType) {
                $attendanceDate = Carbon::parse($attendance->date);
                $isWithinWeek = $attendanceDate->between($weekStart, $weekEnd);
                $matchesType = $filterType === 'both' || $attendance->type === $filterType;
                return $isWithinWeek && $matchesType;
            });

            $sessionsInWeek = $weekAttendances->count();
            $presentInWeek = $weekAttendances->where('status', 'present')->count();
            $weeklyAttendance[$week] = $presentInWeek;

            $totalSessions += $sessionsInWeek;
            $presentSessions += $presentInWeek;
        }

        // Calculate attendance rate
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;

        // Course selection for contribution graph
        $selectedCourseId = $request->input('course_id');
        $selectedCourse = $selectedCourseId ? $currentSemesterCourses->firstWhere('id', $selectedCourseId) : null;

        // Prepare contribution graph data for the selected course
        $contributionData = [];
        if ($selectedCourse) {
            $courseAttendances = $attendances->where('course_id', $selectedCourse->id)->where('type', 'lecture');
            $currentDate = $semesterStart->copy();
            
            while ($currentDate <= $semesterEnd) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayAttendances = $courseAttendances->where('date', $currentDate);
                $attended = $dayAttendances->contains('status', 'present');
                $contributionData[$dateStr] = $attended ? 1 : 0; // 1 for attended, 0 for not attended
                $currentDate->addDay();
            }
        }
        else {
            $currentDate = $semesterStart->copy();
            
            while ($currentDate <= $semesterEnd) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayAttendances = $attendances->where('date', $currentDate);
                $attended = $dayAttendances->contains('status', 'present');
                $contributionData[$dateStr] = $attended ? 1 : 0; // 1 for attended, 0 for not attended
                $currentDate->addDay();
            }
        }

        return view('student.attendance', compact(
            'currentSemesterCourses',
            'weeklyAttendance',
            'attendanceRate',
            'filterType',
            'selectedCourse',
            'semesterStart',
            'semesterEnd',
            'contributionData'
        ));
    }

    public function professorProfile($professorId)
    {
        $user = User::findOrFail($professorId);
        return view('student.professor-profile', compact('user'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
