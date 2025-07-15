<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Assignment_submission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $children = $user->children()->with('enrollments.course')->get();

        return view('parent.home', compact('user', 'children'));
    }

    public function childGrades($childId)
    {
        $child = User::findOrFail($childId);
        $enrollments = $child->enrollments()->with('course.grades')->get();

        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : now()->format('Y');

        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second';
        $currentAcademicYear = $currentYear - $startYear + 1;

        $semesters = [];
        $gpaTrendData = []; // Array to store GPA trend data

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

        // Calculate semester-wise GPA and credit hours
        $semesterStats = [];
        $previousCGPA = 0;
        $totalCredits = 0;
        $totalPoints = 0;

        $gradePoints = [
            'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
            'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
            'D+' => 1.3, 'D' => 1.0, 'D-' => 0.7,
            'F' => 0.0
        ];

        $gradeLabels = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'F'];

        foreach ($semesters as $semesterValue => $semesterLabel) {
            [$year, $semester] = explode('-', $semesterValue);
            $year = (int) str_replace('year', '', $year);
            
            $semesterEnrollments = $enrollments->filter(function ($enrollment) use ($year, $semester, $startYear) {
                $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                return $enrollmentYear === $year && $enrollment->course->semester === $semester;
            });

            $semesterCredits = 0;
            $semesterPoints = 0;

            // Initialize grade distribution for this semester
            $gradeDistribution = array_fill_keys($gradeLabels, 0);

            foreach ($semesterEnrollments as $enrollment) {
                $grade = $enrollment->course->grades->where('student_id', $child->id)->first();
                if ($grade) {
                    $creditHours = $enrollment->course->credit_hours;
                    $semesterCredits += $creditHours;
                    $semesterPoints += ($gradePoints[$grade->grade] ?? 0) * $creditHours;
                    
                    // Count grades for distribution
                    if (isset($gradeDistribution[$grade->grade])) {
                        $gradeDistribution[$grade->grade]++;
                    }
                }
            }

            $semesterGPA = $semesterCredits > 0 ? round($semesterPoints / $semesterCredits, 2) : 0;
            
            $totalCredits += $semesterCredits;
            $totalPoints += $semesterPoints;
            $cgpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

            // Get department statistics for this semester
            $departmentStudents = User::where('major', $child->major)
                ->where('role', 'student')
                ->where('year', $child->year)
                ->count();

            // Calculate average CGPA for the department in this semester
            $departmentGrades = Grade::whereHas('course', function($query) use ($year, $semester, $startYear) {
                $query->where('semester', $semester);
            })
            ->whereHas('student', function($query) use ($child) {
                $query->where('major', $child->major);
            })
            ->get();

            $departmentTotalPoints = 0;
            $departmentTotalCredits = 0;

            foreach ($departmentGrades as $grade) {
                $creditHours = $grade->course->credit_hours;
                $departmentTotalCredits += $creditHours;
                $departmentTotalPoints += ($gradePoints[$grade->grade] ?? 0) * $creditHours;
            }

            $departmentAvgCGPA = $departmentTotalCredits > 0 ? 
                round($departmentTotalPoints / $departmentTotalCredits, 2) : 0;

            // Add GPA trend data
            $actualYear = $startYear + $year - 1;
            $gpaTrendData[] = [
                'semester' => $actualYear . ' ' . ucfirst($semester),
                'gpa' => $semesterGPA
            ];

            $semesterStats[$semesterValue] = [
                'credits' => $semesterCredits,
                'gpa' => $semesterGPA,
                'total_credits' => $totalCredits,
                'cgpa' => $cgpa,
                'cgpa_trend' => $previousCGPA === 0 ? 'same' : ($cgpa > $previousCGPA ? 'up' : ($cgpa < $previousCGPA ? 'down' : 'same')),
                'grade_distribution' => array_values($gradeDistribution),
                'number_of_courses' => $semesterEnrollments->count(),
                'department_stats' => [
                    'total_students' => $departmentStudents,
                    'avg_cgpa' => $departmentAvgCGPA
                ]
            ];

            $previousCGPA = $cgpa;
        }

        // Sort GPA trend data by year and semester
        usort($gpaTrendData, function($a, $b) {
            $yearA = (int)explode(' ', $a['semester'])[0];
            $yearB = (int)explode(' ', $b['semester'])[0];
            if ($yearA === $yearB) {
                return strpos($a['semester'], 'First') !== false ? -1 : 1;
            }
            return $yearA - $yearB;
        });

        return view('parent.child-grades', compact(
            'child', 
            'childId', 
            'enrollments', 
            'startYear', 
            'semesters', 
            'currentSemesterValue',
            'semesterStats',
            'gradeLabels',
            'gpaTrendData'
        ));
    }

    public function childCourses($childId)
    {
        $child = User::findOrFail($childId);
        // Get enrollments for the specific semester (2025-08-01)
        $enrollments = $child->enrollments()
            ->with(['course' => function($query) {
                $query->with(['grades', 'assignments.submissions', 'attendance']);
            }])
            ->where('enrolled_at', '2025-08-01')
            ->get();

        // Prepare course statistics for each enrollment
        $courseStats = [];
        $chartData = [
            'labels' => [],
            'grades' => [],
        ];
        $totalCreditHours = 0;

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $chartData['labels'][] = $course->name;
            $totalCreditHours += $course->credit_hours;
            // Get grade for this course
            $grade = $course->grades->where('student_id', $child->id)->first();
            $chartData['grades'][] = $grade->total;
            
            // Calculate assignment statistics
            $assignments = $course->assignments;
            $submissions = $assignments->flatMap->submissions->where('student_id', $child->id);
            $totalAssignments = $assignments->count();
            $completedAssignments = $submissions->where('status', 'submitted')->count();
            $completionRate = $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100) : 0;
            
            // Calculate attendance statistics
            $attendances = $course->attendance->where('student_id', $child->id);
            $totalSessions = $attendances->count();
            $presentSessions = $attendances->where('status', 'present')->count();
            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
            
            // Calculate course progress
            $remainingSessions = 32 - $totalSessions;
            $courseProgress = $totalSessions / 32 * 100;
            
            $courseStats[$course->id] = [
                'grade' => $grade,
                'completion_rate' => $completionRate,
                'attendance_rate' => $attendanceRate,
                'total_assignments' => $totalAssignments,
                'completed_assignments' => $completedAssignments,
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'remaining_sessions' => $remainingSessions,
                'course_progress' => $courseProgress
            ];
        }

        $predictedGPAs = json_decode(file_get_contents(base_path('python_scripts/results/gpa_predictions.json')), true);
        $childPredictedGPA = round($predictedGPAs[$child->id]['predicted_semester_gpa'], 2);
        $childPredictedCGPA = round($predictedGPAs[$child->id]['predicted_new_cgpa'], 2);

        return view('parent.child-courses', compact(
            'child',
            'childId',
            'enrollments', 
            'courseStats', 
            'chartData', 
            'totalCreditHours',
            'childPredictedGPA',
            'childPredictedCGPA'
        ));
    }

    public function childCourseDetails($courseId, $childId)
    {
        $child = User::findOrFail($childId);
        $course = Course::findOrFail($courseId); 
        $studentPerformance = json_decode(file_get_contents(base_path('python_scripts/results/clustering_results.json')), true);
        $studentPerformance = $studentPerformance[$course->id]['students'][$child->id] ?? null;
        $predictedGrade = 'N/A';
        if ($studentPerformance) {
            $score = $studentPerformance['total_score'];
            if ($score >= 0.8) $predictedGrade = 'A';
            elseif ($score >= 0.67) $predictedGrade = 'B';
            elseif ($score >= 0.5) $predictedGrade = 'C';
            elseif ($score >= 0.4) $predictedGrade = 'D';
            else $predictedGrade = 'F';
        }
        $grade = Grade::where('student_id', $child->id)->where('course_id', $course->id)->firstOrFail(); 

        if (!$grade) {
            abort(404, 'Grade not found for this course.');
        }

        // Calculate average grade for the course
        $allGrades = Grade::where('course_id', $course->id)->get();
        $totalPoints = 0;

        foreach ($allGrades as $courseGrade) {
            $totalPoints += $courseGrade->total ?? 0;
        }

        $averageGrade = $allGrades->count() > 0 ? round($totalPoints / $allGrades->count(), 2) : 0;
        $departmentStudents = $allGrades->count();

        $maxScores = [
            'quiz1' => 10,
            'quiz2' => 10,
            'midterm' => 30,
            'project' => 30,
            'assignments' => 30, 
            'final' => 60,
        ];

        // Prepare scores for display
        $displayScores = [
            'quiz1' => $grade->quiz1 ? $grade->quiz1 : 0,
            'quiz2' => $grade->quiz2 ? $grade->quiz2 : 0,
            'midterm' => $grade->midterm ? $grade->midterm : 0,
            'project' => $grade->project ? $grade->project : 0,
            'assignments' => $grade->assignments ? $grade->assignments : 0,
            'final' => $grade->final ? $grade->final : 0,
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
        $totalScore = $grade->total;

        // Calculate percentage
        $percentage = round(($totalScore / $totalMaxScore) * 100);

        // Get assignments for this course
        $assignments = Assignment::where('course_id', $course->id)->orderBy('created_at', 'asc')
            ->take(3)->get();
        $submissions = Assignment_submission::whereIn('assignment_id', $assignments->pluck('id'))
            ->where('student_id', $child->id)->get();

        $upcomingAssignments = collect([]);
        if($submissions->count() == 2) {
            $upcomingAssignments = [
                'title' => 'Assignment 3',
                'status' => 'upcoming',
            ];
        }
 
         // Calculate assignment statistics
         $totalAssignments = $assignments->count();
         $completedAssignments = $submissions->where('status', 'submitted')->count();
         $completionRate = $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100) : 0;
 
         // Calculate assignment score rate
         $totalAssignmentsScore = $submissions->where('status', 'submitted')->sum('score');
         $maxPossibleScore = $totalAssignments * 10; // Assuming each assignment is worth 10 points
         $scoreRate = $maxPossibleScore > 0 ? round(($totalAssignmentsScore / $maxPossibleScore) * 100) : 0;
 
         // Get attendance records for this course
         $attendances = Attendance::where('course_id', $course->id)
             ->where('student_id', $child->id)
             ->get();
 
         // Calculate attendance statistics
         $totalSessions = $attendances->count();
         $presentSessions = $attendances->where('status', 'present')->count();
         $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
 
         $missedLectures = $attendances->where('type', 'lecture')->where('status', 'absent')->count();
         $missedLabs = $attendances->where('type', 'lab')->where('status', 'absent')->count();
         $lateLectures = $attendances->where('type', 'lecture')->where('status', 'late')->count();
         $lateLabs = $attendances->where('type', 'lab')->where('status', 'late')->count();
         $totalPresent = $attendances->where('status', 'present')->count();
         $totalSessions = $attendances->count();
 
         // Calculate department average attendance
         $departmentAttendances = Attendance::where('course_id', $course->id)
             ->whereHas('student', function($query) use ($child) {
                 $query->where('major', $child->major);
             })
             ->get();
 
         $departmentTotalSessions = $departmentAttendances->count();
         $departmentTotalPresent = $departmentAttendances->where('status', 'present')->count();
         $departmentAverageAttendance = $departmentTotalSessions > 0 ? 
             round(($departmentTotalPresent / $departmentTotalSessions) * 100) : 0;
 
         // Get department students count
         $departmentStudents = User::where('major', $child->major)
             ->where('role', 'student')
             ->where('year', $child->year)
             ->count();

        return view('parent.child-course-details', compact(
            'child', 
            'childId', 
            'course', 
            'studentPerformance',
            'predictedGrade',
            'grade', 
            'displayScores', 
            'displayMaxScores', 
            'totalScore', 
            'totalMaxScore', 
            'percentage',
            'assignments',
            'submissions',
            'upcomingAssignments',
            'completionRate',
            'scoreRate',
            'attendances',
            'attendanceRate',
            'missedLectures',
            'missedLabs',
            'lateLectures',
            'lateLabs',
            'completedAssignments',
            'totalAssignments',
            'maxPossibleScore',
            'totalAssignmentsScore',
            'averageGrade',
            'departmentStudents',
            'totalPresent',
            'totalSessions',
            'departmentAverageAttendance',
            'departmentStudents'
        ));
    }

    public function childAssignments($childId, Request $request)
    {
        $child = User::findOrFail($childId);
        $enrollments = $child->enrollments()->with('course.assignments.submissions')->get();

        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second';
        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : $currentYear;
        $currentAcademicYear = $currentYear - $startYear + 1;

        $currentSemesterCourses = $enrollments
            ->filter(function ($enrollment) use ($currentAcademicYear, $currentSemester, $startYear) {
                $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                return $enrollmentYear === $currentAcademicYear && $enrollment->course->semester === $currentSemester;
            })
            ->pluck('course');
        
        $assignments = $currentSemesterCourses->flatMap->assignments;

        $submissions = $assignments->flatMap->submissions->where('student_id', $child->id);

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

        return view('parent.child-assignments', compact('child', 'childId', 'assignments', 'submissions', 'completionPercentage', 'scorePercentage', 'upcomingAssignments', 'statusFilter', 'searchQuery', 'filteredSubmissions', 'currentSemesterCourses'));
    }

    public function childAttendance($childId, Request $request)
    {
        $child = User::findOrFail($childId);
        $enrollments = $child->enrollments()->with('course')->get();

        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second';
        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : $currentYear;
        $currentAcademicYear = $currentYear - $startYear + 1;

        $semesterStart = Carbon::create($currentYear, $currentSemester === 'first' ? 1 : 8, 1);
        $semesterEnd = Carbon::create($currentYear, $currentSemester === 'first' ? 6 : 11, 30);

        $currentSemesterCourses = $enrollments
            ->filter(function ($enrollment) use ($currentAcademicYear, $currentSemester, $startYear) {
                $enrollmentYear = (int) $enrollment->enrolled_at->format('Y') - $startYear + 1;
                return $enrollmentYear === $currentAcademicYear && $enrollment->course->semester === $currentSemester;
            })
            ->pluck('course');

        $attendances = Attendance::where('student_id', $child->id)
            ->whereIn('course_id', $currentSemesterCourses->pluck('id'))
            ->whereBetween('date', [$semesterStart, $semesterEnd])
            ->get();

        $earliestAttendanceDate = $attendances->max('date');
        $currentWeek = ceil(($semesterStart)->diffInWeeks($earliestAttendanceDate) + 1);

        $filterType = $request->input('type', 'both');
        $weeksInSemester = $semesterStart->diffInWeeks($semesterEnd) + 1;
        $weeklyAttendance = [];
        $totalSessions = 0;
        $presentSessions = 0;
        $missingSessions = 0;

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
            $presentInWeek = $weekAttendances->whereIn('status', ['present', 'late'])->count();
            if($presentInWeek <= 0 and $currentWeek < $week) {
                $presentInWeek = null;
            }
            $missingInWeek = $weekAttendances->where('status', 'absent')->count();
            $weeklyAttendance[$week] = $presentInWeek;

            $totalSessions += $sessionsInWeek;
            $presentSessions += $presentInWeek;
            $missingSessions += $missingInWeek;
        }

        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
        $missingRate = $totalSessions > 0 ? round(($missingSessions / $totalSessions) * 100) : 0;

        $coursesAttendance = [
            'courseId' => [],
            'course' => [],
            'attendance' => [],
            'attendanceRate' => []
        ];
        foreach ($currentSemesterCourses as $course) {
            $coursesAttendance['courseId'][] = $course->id;
            $coursesAttendance['course'][] = $course;

            $attendances = Attendance::where('course_id', $course->id)
                ->where('student_id', $child->id)
                ->get();
            $coursesAttendance['attendance'][] = $attendances;

            $totalSessions = $attendances->count();
            $presentSessions = $attendances->where('status', 'present')->count();
            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
            $coursesAttendance['attendanceRate'][] = $attendanceRate;
        }


        return view('parent.child-attendance', compact(
            'child', 
            'childId', 
            'currentSemesterCourses',
            'weeklyAttendance',
            'attendanceRate',
            'missingSessions',
            'missingRate',
            'filterType',
            'semesterStart',
            'semesterEnd',
            'coursesAttendance'
        ));
    }

    public function childProfile($childId)
    {
        $child = User::findOrFail($childId);
        $grades = $child->grades()->with('course')->get();

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

        return view('parent.child-profile', compact('child', 'childId', 'gpa', 'totalCredits', 'maxCredits'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('parent.profile', compact('user'));
    }

    public function professorProfile($professorId)
    {
        $user = User::findOrFail($professorId);
        return view('parent.professor-profile', compact('user'));
    }
}