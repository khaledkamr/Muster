<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
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
        $enrollments = $child->enrollments()->with('course.grades')->get();

        $startYear = $enrollments->min('enrolled_at') ? $enrollments->min('enrolled_at')->format('Y') : now()->format('Y');

        $currentMonth = 10;
        $currentYear = now()->year;
        $currentSemester = $currentMonth <= 6 ? 'first' : 'second'; 
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

        return view('parent.child-grades', compact('child', 'childId', 'enrollments', 'startYear', 'semesters', 'currentSemesterValue'));
    }

    public function childCourseDetails($childId, $courseId)
    {
        $child = User::findOrFail($childId);
        $course = Course::findOrFail($courseId); 
        $grade = Grade::where('student_id', $child->id)->where('course_id', $course->id)->firstOrFail();

        if (!$grade) {
            abort(404, 'Grade not found for this course.');
        }

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
            'quiz1' => $grade->quiz1,
            'quiz2' => $grade->quiz2,
            'midterm' => $grade->midterm,
            'project' => $grade->project,
            'assignments' => $grade->assignments, 
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

        return view('parent.child-course-details', compact('child', 'childId', 'course', 'grade', 'displayScores', 'displayMaxScores', 'totalScore', 'totalMaxScore', 'percentage'));
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

    public function childAttendance($childId)
    {
        $child = User::findOrFail($childId);
        // Fetch attendance logic here
        return view('parent.child-attendance', compact('child', 'childId'));
    }

    public function childProfile($childId)
    {
        $child = User::findOrFail($childId);
        // Fetch profile data logic here
        return view('parent.child-profile', compact('child', 'childId'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('parent.profile', compact('user'));
    }
}