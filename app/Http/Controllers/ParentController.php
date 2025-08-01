<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Assignment_submission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Feedback;
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

    public function childGrades($childId, Request $request)
    {
        $child = User::findOrFail($childId);
        $startYear = $child->enrollments()->min('enrolled_at') ? Carbon::parse($child->enrollments()->min('enrolled_at'))->format('Y') : now()->format('Y');

        $semesters = $child->enrollments()
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->selectRaw("
                DISTINCT CONCAT(
                    enrollments.enrolled_at
                ) as semester_value,
                CONCAT(
                    'Year ', 
                    YEAR(enrollments.enrolled_at) - ? + 1, 
                    ' - ', 
                    CASE courses.semester 
                        WHEN 'first' THEN 'First Semester' 
                        ELSE 'Second Semester' 
                    END
                ) as semester_label
            ", [$startYear])->pluck('semester_label', 'semester_value')->toArray();

        $selectedSemester = $request->input('semester', null);
        if ($selectedSemester !== null) {
            $grades = $child->grades()
            ->whereHas('course', function($query) use ($child, $selectedSemester) {
                $query->whereHas('enrollments', function($q) use ($child, $selectedSemester) {
                    $q->where('student_id', $child->id)->where('enrolled_at', $selectedSemester);
                });
            })
            ->with('course')
            ->get();
        } else {
            $grades = collect([]);
        }

        $gradesDistribution = [];
        if(!$grades->contains('grade', null)) {
            $gradesDistribution = $grades->groupBy('grade')
                ->map(function ($grades) {
                    return $grades->count();
                })
                ->toArray();
        }

        $gpaPoints = [
            'A+' => 4.0, 'A' => 3.7, 'A-' => 3.3,
            'B+' => 3.0, 'B' => 2.7, 'B-' => 2.3,
            'C+' => 2.0, 'C' => 1.7, 'C-' => 1.3,
            'D+' => 1.0, 'D' => 0.7, 'F' => 0.0
        ];

        $gpaTrend = [];
        $cgpaTrend = [];
        foreach($semesters as $semesterValue => $semesterLabel) {
            $semesterGrades = $child->grades()
            ->whereHas('course', function($query) use ($child, $semesterValue) {
                $query->whereHas('enrollments', function($q) use ($child, $semesterValue) {
                $q->where('student_id', $child->id)->where('enrolled_at', $semesterValue);
                });
            })
            ->with('course')
            ->get();

            if ($semesterGrades->isNotEmpty() && $semesterGrades[0]->grade !== null) {
                $totalPoints = 0;
                $totalCredits = $semesterGrades->sum('course.credit_hours');
                foreach ($semesterGrades as $grade) {
                    $totalPoints += ($gpaPoints[$grade->grade] ?? 0) * ($grade->course->credit_hours ?? 0);
                }
                $gpaTrend[$semesterValue] = [
                    'gpa' => round($totalPoints / $totalCredits, 2),
                    'total_credits' => $totalCredits,
                ];
            } else {
                $totalCredits = $semesterGrades->sum('course.credit_hours');
                $gpaTrend[$semesterValue] = [
                    'gpa' => null,
                    'total_credits' => $totalCredits,
                ];
            }

            $allGrades = $child->grades()
            ->whereHas('course', function($query) use ($child, $semesterValue) {
                $query->whereHas('enrollments', function($q) use ($child, $semesterValue) {
                $q->where('student_id', $child->id)->where('enrolled_at', '<=', $semesterValue);
                });
            })
            ->with('course')
            ->get();
            
            if ($allGrades->isNotEmpty() && !$allGrades->contains('grade', null)) {
                $allTotalPoints = 0;
                $allTotalCredits = $allGrades->sum('course.credit_hours');
                foreach ($allGrades as $grade) {
                    $allTotalPoints += ($gpaPoints[$grade->grade] ?? 0) * ($grade->course->credit_hours ?? 0);
                }
                
                $prevCgpa = null;
                if (count($cgpaTrend) > 0) {
                    $prevCgpa = collect($cgpaTrend)->last()['cgpa'] ?? null;
                }
                $currentCgpa = round($allTotalPoints / $allTotalCredits, 2);
                $cgpaStatus = 'same';
                if ($prevCgpa !== null) {
                    if ($currentCgpa > $prevCgpa) {
                        $cgpaStatus = 'up';
                    } elseif ($currentCgpa < $prevCgpa) {
                        $cgpaStatus = 'down';
                    }
                }
                $cgpaTrend[$semesterValue] = [
                    'cgpa' => $currentCgpa,
                    'status' => $cgpaStatus,
                    'total_credits' => $allTotalCredits
                ];
            } else {
                $allTotalCredits = $allGrades->sum('course.credit_hours');
                $cgpaTrend[$semesterValue] = [
                    'cgpa' => null,
                    'status' => 'same',
                    'total_credits' => $allTotalCredits
                ];
            }
        }
        
        $semesterStatistics = [];
        if ($selectedSemester !== null) {
            $departmentStudents = User::where('major', $child->major)->where('role', 'student')->count();

            $departmentGrades = Grade::whereHas('student', function($query) use ($child) {
                $query->where('major', $child->major)
                    ->where('role', 'student');
            })->with(['student', 'course'])->get();

            $departmentPoints = $departmentGrades->sum(function($grade) use ($gpaPoints) {
                return ($gpaPoints[$grade->grade] ?? 0) * $grade->course->credit_hours;
            });

            $departmentCredits = $departmentGrades->sum('course.credit_hours');
            $departmentAvgCGPA = $departmentCredits > 0 ? 
            round($departmentPoints / $departmentCredits, 2) : 0;

            $semesterStatistics = [
                'semester_credits' => $gpaTrend[$selectedSemester]['total_credits'] ?? 0,
                'semester_gpa' => $gpaTrend[$selectedSemester]['gpa'] ?? null,
                'total_credits' => $cgpaTrend[$selectedSemester]['total_credits'] ?? 0,
                'cgpa' => $cgpaTrend[$selectedSemester]['cgpa'] ?? null,
                'cgpa_status' => $cgpaTrend[$selectedSemester]['status'] ?? 'same',
                'department_students' => $departmentStudents,
                'department_avg_cgpa' => $departmentAvgCGPA
            ];
        }

        $gradeLabels = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'F'];

        return view('parent.child-grades', compact(
            'child', 
            'childId', 
            'semesters', 
            'selectedSemester',
            'grades',
            'semesterStatistics',
            'gradeLabels',
            'gradesDistribution',
            'cgpaTrend',
            'gpaTrend'
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

        $upcomingAssignments = collect([
            'title' => null,
            'status' => null
        ]);
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
        $feedbacks = Feedback::where('about', $child->id)->get();
        $grades = $child->grades()->with('course')->get();

        $gradePoints = [
            'A+' => 4.0, 'A'  => 4.8, 'A-' => 3.7,
            'B+' => 3.3, 'B'  => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C'  => 2.0, 'C-' => 1.7,
            'D+' => 1.3, 'D'  => 1.0, 'D-' => 0.7,
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

        return view('parent.child-profile', compact('child', 'childId', 'gpa', 'totalCredits', 'maxCredits', 'feedbacks'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('parent.profile', compact('user'));
    }

    public function professorProfile($professorId)
    {
        $user = User::findOrFail($professorId);
        $feedbacks = Feedback::where('about', $user->id)->get();
        return view('parent.professor-profile', compact('user', 'feedbacks'));
    }
}