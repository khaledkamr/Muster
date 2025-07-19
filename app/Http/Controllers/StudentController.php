<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\Assignment_submission;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

        $earliestAttendanceDate = $attendances->max('date');
        $currentWeek = ceil(($semesterStart)->diffInWeeks($earliestAttendanceDate) + 1);

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
            $presentInWeek = $weekAttendances->whereIn('status', ['present', 'late'])->count();
            if($presentInWeek <= 0 and $currentWeek < $week) {
                $presentInWeek = null;
            }
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

    public function courses()
    {
        $user = Auth::user();
        
        $enrollments = $user->enrollments()
            ->with(['course' => function($query) {
                $query->with(['grades', 'assignments.submissions', 'attendance']);
            }])
            ->where('enrolled_at', '2025-08-01')
            ->get();

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
            $grade = $course->grades->where('student_id', $user->id)->first();
            $chartData['grades'][] = $grade->total;
            
            $assignments = $course->assignments;
            $submissions = $assignments->flatMap->submissions->where('student_id', $user->id);
            $totalAssignments = $assignments->count();
            $completedAssignments = $submissions->where('status', 'submitted')->count();
            $completionRate = $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100) : 0;
            
            $attendances = $course->attendance->where('student_id', $user->id);
            $totalSessions = $attendances->count();
            $presentSessions = $attendances->whereIn('status', ['present', 'late'])->count();
            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
            
            $remainingSessions = 32 - $totalSessions;
            $courseProgress = $totalSessions / 32 * 100;
            
            $courseStats[$course->id] = [
                'grade' => $grade,
                'total_assignments' => $totalAssignments,
                'completed_assignments' => $completedAssignments,
                'completion_rate' => $completionRate,
                'attendance_rate' => $attendanceRate,
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'remaining_sessions' => $remainingSessions,
                'course_progress' => $courseProgress
            ];
        }

        $recommendation_data = $this->recommend_courses($user->id);
        $recommendedElectives = $recommendation_data['recommendations'];

        $predicted_data = $this->gpa_predict($user->id);
        $predictedGPA = $predicted_data['predicted_semester_gpa'];
        $predictedCGPA = $predicted_data['predicted_new_cgpa'];

        return view('student.courses', compact('enrollments', 'courseStats', 'chartData', 'totalCreditHours', 'recommendedElectives' , 'predictedGPA', 'predictedCGPA'));
    }

    public function gpa_predict($student_id) 
    {
        try {
            $response = Http::timeout(10)->post('http://localhost:5000/gpa', [
                'student_id' => $student_id,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return collect($data); 
            }

            return collect([]);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return collect([]);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    public function recommend_courses($student_id) 
    {
        try {
            $response = Http::timeout(10)->post('http://localhost:5000/recommend_courses', [
                'student_id' => $student_id,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return collect($data); 
            }

            return collect([]);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return collect([]);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    public function grades(Request $request)
    {
        $user = Auth::user();
        $startYear = $user->enrollments()->min('enrolled_at') ? Carbon::parse($user->enrollments()->min('enrolled_at'))->format('Y') : now()->format('Y');

        $semesters = $user->enrollments()
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
            $grades = $user->grades()
            ->whereHas('course', function($query) use ($user, $selectedSemester) {
                $query->whereHas('enrollments', function($q) use ($user, $selectedSemester) {
                    $q->where('student_id', $user->id)->where('enrolled_at', $selectedSemester);
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

        $gpaTrendCacheKey = 'gpa_trend_' . $user->id;
        $cgpaTrendCacheKey = 'cgpa_trend_' . $user->id;

        $gpaTrend = Cache::remember($gpaTrendCacheKey, now()->addHours(1), function () use ($user, $semesters, $gpaPoints) {
            $gpaTrend = [];
            foreach($semesters as $semesterValue => $semesterLabel) {
                $semesterGrades = $user->grades()
                    ->whereHas('course', function($query) use ($user, $semesterValue) {
                        $query->whereHas('enrollments', function($q) use ($user, $semesterValue) {
                            $q->where('student_id', $user->id)->where('enrolled_at', $semesterValue);
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
            }
            return $gpaTrend;
        });

        $cgpaTrend = Cache::remember($cgpaTrendCacheKey, now()->addHours(1), function () use ($user, $semesters, $gpaPoints) {
            $cgpaTrend = [];
            foreach($semesters as $semesterValue => $semesterLabel) {
                $allGrades = $user->grades()
                    ->whereHas('course', function($query) use ($user, $semesterValue) {
                        $query->whereHas('enrollments', function($q) use ($user, $semesterValue) {
                            $q->where('student_id', $user->id)->where('enrolled_at', '<=', $semesterValue);
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
            return $cgpaTrend;
        });
        
        $semesterStatistics = [];
        if ($selectedSemester !== null) {
            $departmentStudents = User::where('major', $user->major)->where('role', 'student')->count();

            $departmentGrades = Grade::whereHas('student', function($query) use ($user) {
                $query->where('major', $user->major)
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
        
        return view('student.grades', compact(
            'user', 
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

        $gradePoints = ['A+' => 4.0, 'A'  => 4.8, 'A-' => 3.7, 'B+' => 3.3, 'B'  => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C'  => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D'  => 1.0, 'D-' => 0.7, 'F'  => 0.0, ];

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
        $studentPerformance = json_decode(file_get_contents(base_path('python_scripts/results/clustering_results.json')), true);
        $studentPerformance = $studentPerformance[$course->id]['students'][$user->id] ?? null;
        $predictedGrade = 'N/A';
        if ($studentPerformance) {
            $score = $studentPerformance['total_score'];
            if ($score >= 0.8) $predictedGrade = 'A';
            elseif ($score >= 0.67) $predictedGrade = 'B';
            elseif ($score >= 0.5) $predictedGrade = 'C';
            elseif ($score >= 0.4) $predictedGrade = 'D';
            else $predictedGrade = 'F';
        }
        $grade = Grade::where('student_id', $user->id)->where('course_id', $course->id)->firstOrFail(); 

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
            ->where('student_id', $user->id)->get();

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
            ->where('student_id', $user->id)
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
            ->whereHas('student', function($query) use ($user) {
                $query->where('major', $user->major);
            })
            ->get();

        $departmentTotalSessions = $departmentAttendances->count();
        $departmentTotalPresent = $departmentAttendances->where('status', 'present')->count();
        $departmentAverageAttendance = $departmentTotalSessions > 0 ? 
            round(($departmentTotalPresent / $departmentTotalSessions) * 100) : 0;

        // Get department students count
        $departmentStudents = User::where('major', $user->major)
            ->where('role', 'student')
            ->where('year', $user->year)
            ->count();

        return view('student.course-details', compact(
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

    public function attendance(Request $request)
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()->with('course')->get();

        $currentMonth = 10;
        $currentYear = 2025;
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

        $earliestAttendanceDate = $attendances->max('date');
        $currentWeek = ceil(($semesterStart)->diffInWeeks($earliestAttendanceDate) + 1);

        // Calculate weekly attendance (lectures, labs, or both)
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
                ->where('student_id', $user->id)
                ->get();
            $coursesAttendance['attendance'][] = $attendances;

            $totalSessions = $attendances->count();
            $presentSessions = $attendances->where('status', 'present')->count();
            $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
            $coursesAttendance['attendanceRate'][] = $attendanceRate;
        }

        return view('student.attendance', compact(
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

    public function professorProfile($professorId)
    {
        $user = User::findOrFail($professorId);
        return view('student.professor-profile', compact('user'));
    }
}
