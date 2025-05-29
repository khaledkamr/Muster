<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Assignment_submission;


class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $professor = Auth::user();
        $courses = $professor->courses()->get();

        // Attendance Summary
        $totalSessions = $courses->sum(function ($course) {
            return $course->attendance->count();
        });
        $presentCount = $courses->sum(function ($course) {
            return $course->attendance->where('status', 'present')->count();
        });
        $attendanceRate = $totalSessions > 0 ? ($presentCount / $totalSessions) * 100 : 0;

        // Assignment Status
        $totalSubmissions = $courses->flatMap(function ($course) {
            return $course->assignments->flatMap->submissions;
        })->count();
        $submittedCount = $courses->flatMap(function ($course) {
            return $course->assignments->flatMap->submissions->where('status', 'submitted');
        })->count();
        $pendingCount = $totalSubmissions - $submittedCount;

        // Upcoming Events (Placeholder Data)
        $upcomingEvents = [
            ['title' => 'Midterm Exam', 'date' => '2025-05-10'],
            ['title' => 'Class Meeting', 'date' => '2025-05-12'],
        ];

        // Attendance Records
        $allAttendanceRecords = $courses[0] ? $courses[0]->attendance->filter(function ($attendance) {
            return Carbon::parse($attendance->date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01'));
        }) : collect();

        $assignmentSubmissions = $courses[0] ? $courses[0]->assignments
            ->filter(function ($assignment) {
                return Carbon::parse($assignment->due_date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01'));
            })
            ->flatMap(function ($assignment) {
                return $assignment->submissions;
            }) : collect();
        
        return view('professor.index', compact('professor', 'courses', 'attendanceRate', 'totalSubmissions', 'submittedCount', 'pendingCount', 'upcomingEvents', 'allAttendanceRecords', 'assignmentSubmissions'));
    }

    public function dashboard($courseId) 
    {   
        $course = $courseId ? Course::findOrFail($courseId) : null;
        $students = $course ? $course->enrollments->where('enrolled_at', Carbon::parse('2025-08-01'))
            ->map(function ($enrollment) {
                return $enrollment->student;
            }) : collect();

        $lastYearStudents = $course ? $course->enrollments->where('enrolled_at', Carbon::parse('2024-08-01'))
        ->map(function ($enrollment) {
            return $enrollment->student;
        }) : collect();

        // Calculate percentage difference
        $currentCount = $students->count();
        $lastYearCount = $lastYearStudents->count() - 5;
        $percentageDiff = $lastYearCount > 0 ? (($currentCount - $lastYearCount) / $lastYearCount) * 100 : 0;

        //top 5 students
        $top5Students = $course ? $course->enrollments->where('enrolled_at', '>=', Carbon::parse('2025-08-01'))->sortByDesc(function ($enrollment) use ($course) {
            return $enrollment->student->grades->where('course_id', $course->id)->first()->total;
        })->take(5) : collect();
        $top5Students = $top5Students->map(function ($enrollment) use ($course) {
            return $enrollment->student;
        });

        // Calculate attendance rate
        $totalSessions = 20;
        $attendanceRecords = $course ? $course->attendance->where('date', '>=', Carbon::parse('2025-08-01'))->count() : 0;
        $presentCount = $course ? $course->attendance->where('date', '>=', Carbon::parse('2025-08-01'))
            ->where('status', 'present')
            ->count() : 0;
        $attendanceRate = $totalSessions > 0 ? ($presentCount / $attendanceRecords) * 100 : 0;

        $weeks = [
            'week1' => ['start' => Carbon::parse('2025-08-01'),'end' => Carbon::parse('2025-08-07')],
            'week2' => ['start' => Carbon::parse('2025-08-08'),'end' => Carbon::parse('2025-08-14')],
            'week3' => ['start' => Carbon::parse('2025-08-15'),'end' => Carbon::parse('2025-08-21')],
            'week4' => ['start' => Carbon::parse('2025-08-22'),'end' => Carbon::parse('2025-08-28')],
            'week5' => ['start' => Carbon::parse('2025-08-29'),'end' => Carbon::parse('2025-09-04')],
            'week6' => ['start' => Carbon::parse('2025-09-05'),'end' => Carbon::parse('2025-09-11')],
            'week7' => ['start' => Carbon::parse('2025-09-12'),'end' => Carbon::parse('2025-09-18')],
            'week8' => ['start' => Carbon::parse('2025-09-19'),'end' => Carbon::parse('2025-09-25')],
            'week9' => ['start' => Carbon::parse('2025-09-26'),'end' => Carbon::parse('2025-10-02')],
            'week10' => ['start' => Carbon::parse('2025-10-03'),'end' => Carbon::parse('2025-10-09')],
            'week11' => ['start' => Carbon::parse('2025-10-10'),'end' => Carbon::parse('2025-10-16')],
        ];

        $weeklyAttendance = [];
        $allAttendanceRecords = $course ? $course->attendance->filter(function ($attendance) {
            return Carbon::parse($attendance->date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01'));
        }) : collect();

        foreach ($weeks as $weekKey => $week) {
            $weekRecords = $allAttendanceRecords->filter(function ($record) use ($week) {
                return Carbon::parse($record->date)->betweenIncluded($week['start'], $week['end']);
            });
            $weeklyAttendance[$weekKey] = [
                'present' => $weekRecords->where('status', 'present')->count(),
                'absent' => $weekRecords->where('status', 'absent')->count(),
                'late' => $weekRecords->where('status', 'late')->count(),
            ];
        }

        $presentCount = $allAttendanceRecords->where('status', 'present')->count();
        $absentCount = $allAttendanceRecords->where('status', 'absent')->count();
        $lateCount = $allAttendanceRecords->where('status', 'late')->count();

        // Assignment Status
        $totalAssignments = $course ? $course->assignments->count() : 0;
        $submittedAssignments = $course ? $course->assignments->flatMap->submissions->where('status', 'submitted')->count() : 0;
        $totalSubmissions = $course ? $course->assignments->flatMap->submissions->count() : 0;

        $submissionRate = $totalSubmissions > 0 ? ($submittedAssignments / $totalSubmissions) * 100 : 0;

        return view('professor.dashboard', compact(
            'course', 
            'courseId', 
            'students', 
            'lastYearStudents', 
            'percentageDiff',
            'attendanceRate',
            'totalSessions',
            'weeklyAttendance',
            'submissionRate',
            'totalAssignments',
            'top5Students',
            'presentCount',
            'absentCount',
            'lateCount'
        ));
    }

    public function students($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $students = $course ? $course->enrollments
            ->where('enrolled_at', Carbon::parse('2025-08-01'))
            ->map(function ($enrollment) {
                return $enrollment->student;
            }) : collect();

        $searchQuery = request()->query('search');
        if ($searchQuery) {
            $students = $students->filter(function ($student) use ($searchQuery) {
                return stripos($student->id, $searchQuery) !== false || stripos($student->name, $searchQuery) !== false;
            });
        }

        // Convert collection to paginator with 10 items per page
        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $students->forPage(request()->get('page', 1), 50),
            $students->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('professor.students', compact('course', 'courseId', 'students'));
    }

    public function exams($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $grades = $course->grades()
            ->whereHas('student.enrollments', function($query) use ($course) {
                $query->where('course_id', $course->id)->where('enrolled_at', '>=', '2025-08-01');
            })
            ->with('student')
            ->get();

        $examTypes = [
            'quiz1' => 'Quiz 1',
            'quiz2' => 'Quiz 2',
            'midterm' => 'Midterm',
            'project' => 'Project',
            'assignments' => 'Assignments',
            'final' => 'Final'
        ];

        $examStats = [];
        $chartData = [
            'labels' => [],
            'averages' => [],
            'colors' => []
        ];

        foreach ($examTypes as $field => $name) {
            $examGrades = $grades->map(function ($grade) use ($field) {
                return [
                    'student_id' => $grade->student->id,
                    'student_name' => $grade->student->name,
                    'grade' => $grade->$field
                ];
            })->filter(function ($item) {
                return $item['grade'] !== null;
            })->sortByDesc('grade');

            if ($examGrades->isNotEmpty()) {
                $maxGrade = $examGrades->first();
                $minGrade = $examGrades->last();
                $avgGrade = $examGrades->avg('grade');

                // Convert collection to paginator
                $paginatedGrades = new \Illuminate\Pagination\LengthAwarePaginator(
                    $examGrades->forPage(request()->get('page', 1), 10),
                    $examGrades->count(),
                    10,
                    request()->get('page', 1),
                    ['path' => request()->url(), 'query' => request()->query()]
                );

                $examStats[$field] = [
                    'name' => $name,
                    'max_grade' => [
                        'value' => $maxGrade['grade'],
                        'student_name' => $maxGrade['student_name']
                    ],
                    'min_grade' => [
                        'value' => $minGrade['grade'],
                        'student_name' => $minGrade['student_name']
                    ],
                    'average_grade' => round($avgGrade, 2),
                    'grades' => $paginatedGrades
                ];

                // Add data for chart
                $chartData['labels'][] = $name;
                $chartData['averages'][] = round($avgGrade, 2);
                $chartData['colors'][] = $this->getChartColor($field);
            } else {
                $examStats[$field] = [
                    'name' => $name,
                    'max_grade' => [
                        'value' => null,
                        'student_name' => null
                    ],
                    'min_grade' => [
                        'value' => null,
                        'student_name' => null
                    ],
                    'average_grade' => 0,
                    'grades' => null
                ];

                // Add zero data for chart
                $chartData['labels'][] = $name;
                $chartData['averages'][] = 0;
                $chartData['colors'][] = $this->getChartColor($field);
            }
        }

        return view('professor.exams', compact('course', 'courseId', 'examStats', 'chartData'));
    }

    private function getChartColor($examType)
    {
        $colors = [
            'quiz1' => 'rgba(54, 162, 235, 0.8)',
            'quiz2' => 'rgba(75, 192, 192, 0.8)',
            'midterm' => 'rgba(153, 102, 255, 0.8)',
            'project' => 'rgba(255, 159, 64, 0.8)',
            'assignments' => 'rgba(255, 99, 132, 0.8)',
            'final' => 'rgba(255, 206, 86, 0.8)'
        ];

        return $colors[$examType] ?? 'rgba(201, 203, 207, 0.8)';
    }

    public function grades($courseId)
    {
        $course = Course::findOrFail($courseId);
        $searchQuery = request()->query('search');

        $students = $course->enrollments()
            ->where('enrolled_at', '>=', Carbon::parse('2025-08-01')) 
            ->with(['student', 'student.grades' => function ($query) use ($courseId) {
                $query->where('course_id', $courseId); 
            }])
            ->get()
            ->map(function ($enrollment) {
                return $enrollment->student;
            });

        if ($searchQuery) {
            $students = $students->filter(function ($student) use ($searchQuery) {
                return stripos($student->id, $searchQuery) !== false || stripos($student->name, $searchQuery) !== false;
            });
        }

        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $students->forPage(request()->get('page', 1), 50),
            $students->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $sortedStudents = $course ? $course->enrollments->where('enrolled_at', '>=', Carbon::parse('2025-08-01'))->sortByDesc(function ($enrollment) use ($course) {
            return $enrollment->student->grades->where('course_id', $course->id)->first()->total;
        }) : collect();
        $sortedStudents = $sortedStudents->map(function ($enrollment) use ($course) {
            return $enrollment->student;
        });

        $stats = [
            'max_grade' => [
                'value' => $sortedStudents->first()->grades->where('course_id', $course->id)->first()->total,
                'student_name' => $sortedStudents->first()->name
            ],
            'min_grade' => [
                'value' => $sortedStudents->last()->grades->where('course_id', $course->id)->first()->total,
                'student_name' => $sortedStudents->last()->name
            ],
            'average_grade' => 0,
            'grades' => $students
        ];

        $totalGrades = 0;
        $validGradeCount = 0;

        foreach ($students as $student) {
            $grade = $student->grades->first();
            if ($grade) {
                $total = $grade->total;
                $totalGrades += $total;
                $validGradeCount++;
            }
        }

        $stats['average_grade'] = $validGradeCount > 0 ? round($totalGrades / $validGradeCount, 1) : 0;

        return view('professor.grades', compact('course', 'courseId', 'students', 'stats'));
    }

    public function assignments($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $view = request('view', 'assign1');

        $submissions = $course ? $course->assignments
            ->filter(function ($assignment) use ($view) {
                return Carbon::parse($assignment->due_date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01')) 
                    && $assignment->title === ($view === 'assign1' ? 'Assignment 1' : 'Assignment 2');
            })
            ->flatMap(function ($assignment) {
                return $assignment->submissions;
            }) : collect();

        $statusFilter = request('status', 'all');
        $searchQuery = request('search', '');

        $filteredSubmissions = $submissions;

        if ($statusFilter === 'submitted') {
            $filteredSubmissions = $filteredSubmissions->where('status', 'submitted');
        } elseif ($statusFilter === 'pending') {
            $filteredSubmissions = $filteredSubmissions->where('status', 'pending');
        }

        if ($searchQuery) {
            $filteredSubmissions = $filteredSubmissions->filter(function ($submission) use ($searchQuery) {
                return stripos($submission->user->id, $searchQuery) !== false;
            });
        }

        $filteredSubmissions = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredSubmissions->forPage(request()->get('page', 1), 50),
            $filteredSubmissions->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('professor.assignments', compact('course', 'courseId', 'filteredSubmissions', 'statusFilter', 'searchQuery'));
    }

    public function attendance($courseId)
    {
        $course = $courseId ? Course::find($courseId) : null;
        $view = request('view', 'week1');
        $weeks = [
            'week1' => ['start' => Carbon::parse('2025-08-01'),'end' => Carbon::parse('2025-08-07')],
            'week2' => ['start' => Carbon::parse('2025-08-08'),'end' => Carbon::parse('2025-08-14')],
            'week3' => ['start' => Carbon::parse('2025-08-15'),'end' => Carbon::parse('2025-08-21')],
            'week4' => ['start' => Carbon::parse('2025-08-22'),'end' => Carbon::parse('2025-08-28')],
            'week5' => ['start' => Carbon::parse('2025-08-29'),'end' => Carbon::parse('2025-09-04')],
            'week6' => ['start' => Carbon::parse('2025-09-05'),'end' => Carbon::parse('2025-09-11')],
            'week7' => ['start' => Carbon::parse('2025-09-12'),'end' => Carbon::parse('2025-09-18')],
            'week8' => ['start' => Carbon::parse('2025-09-19'),'end' => Carbon::parse('2025-09-25')],
            'week9' => ['start' => Carbon::parse('2025-09-26'),'end' => Carbon::parse('2025-10-02')],
            'week10' => ['start' => Carbon::parse('2025-10-03'),'end' => Carbon::parse('2025-10-09')],
            'week11' => ['start' => Carbon::parse('2025-10-10'),'end' => Carbon::parse('2025-10-16')],
        ];

        $attendanceRecords = $course ? $course->attendance
            ->filter(function ($attendance) use ($view, $weeks) {
                return Carbon::parse($attendance->date)->greaterThanOrEqualTo($weeks[$view]['start']) 
                    && Carbon::parse($attendance->date)->lessThanOrEqualTo($weeks[$view]['end']);
            }) : collect();

        $weeklyAttendance = [];
        $allAttendanceRecords = $course ? $course->attendance->filter(function ($attendance) {
            return Carbon::parse($attendance->date)->greaterThanOrEqualTo(Carbon::parse('2025-08-01'));
        }) : collect();

        foreach ($weeks as $weekKey => $week) {
            $weekRecords = $allAttendanceRecords->filter(function ($record) use ($week) {
                return Carbon::parse($record->date)->betweenIncluded($week['start'], $week['end']);
            });
            $weeklyAttendance[$weekKey] = [
                'present' => $weekRecords->where('status', 'present')->count(),
                'absent' => $weekRecords->where('status', 'absent')->count(),
                'late' => $weekRecords->where('status', 'late')->count(),
            ];
        }

        $statusFilter = request('status', 'all');
        if($statusFilter === 'present') {
            $attendanceRecords = $attendanceRecords->where('status', 'present');
        } elseif ($statusFilter === 'absent') {
            $attendanceRecords = $attendanceRecords->where('status', 'absent');
        }
        elseif ($statusFilter === 'late') {
            $attendanceRecords = $attendanceRecords->where('status', 'late');
        }

        $typeFilter = request('type', 'all');
        if($typeFilter === 'lecture') {
            $attendanceRecords = $attendanceRecords->where('type', 'lecture');
        } elseif ($typeFilter === 'lab') {
            $attendanceRecords = $attendanceRecords->where('type', 'lab');
        }

        $searchQuery = request('search', '');
        if ($searchQuery) {
            $attendanceRecords = $attendanceRecords->filter(function ($attendance) use ($searchQuery) {
                return stripos($attendance->student->id, $searchQuery) !== false;
            });
        }

        $attendanceRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $attendanceRecords->forPage(request()->get('page', 1), 50),
            $attendanceRecords->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('professor.attendance', compact('course', 'courseId', 'attendanceRecords', 'view', 'statusFilter', 'typeFilter', 'searchQuery', 'weeklyAttendance', 'weeks'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('professor.profile', compact('user'));
    }

    public function studentProfile($studentId, $courseId)
    {
        $student = User::findOrFail($studentId);
        $grades = $student->grades()->with('course')->get();

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

        return view('professor.student-profile', compact('student', 'courseId', 'gpa', 'totalCredits', 'maxCredits'));
    }

    public function courseStudentDetails($courseId, $studentId)
    {
        $user = User::findOrFail($studentId);
        $course = Course::findOrFail($courseId);
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

        return view('professor.course-student-details', compact(
            'course', 
            'user',
            'grade', 
            'displayScores', 
            'displayMaxScores', 
            'totalScore', 
            'totalMaxScore', 
            'percentage',
            'assignments',
            'submissions',
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
}

