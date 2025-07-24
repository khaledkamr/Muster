<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Unique;

class AdminController extends Controller
{
    public function index(Request $request) {
        $users = User::all();
        $usersCount = $users->count();
        $studentsCount = $users->where('role', 'student')->count();
        $professorsCount = $users->where('role', 'professor')->count();
        $adminsCount = $users->where('role', 'admin')->count();

        $userRegistrationTrend = [
            Enrollment::where('enrolled_at', '2022-01-01')->count(),
            Enrollment::where('enrolled_at', '2022-08-01')->count(),
            Enrollment::where('enrolled_at', '2023-01-01')->count(),
            Enrollment::where('enrolled_at', '2023-08-01')->count(),
            Enrollment::where('enrolled_at', '2024-01-01')->count(),
            Enrollment::where('enrolled_at', '2024-08-01')->count(),
            Enrollment::where('enrolled_at', '2025-01-01')->count(),
            Enrollment::where('enrolled_at', '2025-08-01')->count()
        ];

        $studentDistribution = [
            $users->where('year', 'freshman')->count(),
            $users->where('year', 'sophomore')->count(),
            $users->where('year', 'junior')->count(),
            $users->where('year', 'senior')->count(),
        ];

        $currentSemesterCourses = Course::where('semester', 'second')
            ->withCount(['enrollments' => function($query) {
                $query->where('enrolled_at', '2025-08-01');
            }])
            ->whereHas('enrollments', function($query) {
                $query->where('enrolled_at', '2025-08-01');
            })
            ->get();
        
        $topCourses = $currentSemesterCourses->sortByDesc('enrollments_count')->take(5);
        $topFiveCourses = [
            'labels' => $topCourses->pluck('code')->toArray(),
            'data' => $topCourses->pluck('enrollments_count')->toArray(),
        ];

        $passFailPercentage = Course::where('code', 'LIKE', '__3%')->get()->take(6)->map(function($course) {
            $passCount = $course->grades()->where('status', 'pass')->count();
            $failCount = $course->grades()->where('status', 'fail')->count();
            $totalGrades = $passCount + $failCount;
            if($totalGrades == 0) {
                $course->passPercentage = 0;
                $course->failPercentage = 0;
                return $course;
            } else {
                $passPercentage = $passCount / $totalGrades * 100;
                $failPercentage = $failCount / $totalGrades * 100;
                $course->passPercentage = $passPercentage;
                $course->failPercentage = $failPercentage;
                return $course;
            }
        });
        
        $clusterCourse = $currentSemesterCourses->first();
        if($request->input('course')) {
            $courseId =  $request->input('course');
            $clusterCourse = Course::findOrFail($courseId);
        }
        // return $clusterCourse;
        $students = $clusterCourse ? $clusterCourse->enrollments
            ->where('enrolled_at', Carbon::parse('2025-08-01'))
            ->map(function ($enrollment) {
                return $enrollment->student;
            }) : collect();

        // return $students;

        $data = $this->cluster($students->pluck('id'), $clusterCourse->id);

        // return $data;

        $studentsPerformance = [
            $data["high_performers_count"] ?? 0,
            $data["average_performers_count"] ?? 0,
            $data["at_risk_students_count"] ?? 0
        ];
        $highPerformersCount = $data["high_performers_count"] ?? 0;
        $averagePerformersCount = $data["average_performers_count"] ?? 0;
        $atRiskStudentsCount = $data["at_risk_students_count"] ?? 0;


        return view('admin.index', compact(
            'usersCount' ,
            'studentsCount',
            'professorsCount',
            'adminsCount',
            'userRegistrationTrend',
            'studentDistribution',
            'topFiveCourses',
            'passFailPercentage',
            'highPerformersCount',
            'averagePerformersCount',
            'atRiskStudentsCount',
            'studentsPerformance',
            'currentSemesterCourses'
        ));
    }

    public function cluster($studentIds, $courseId) 
    {
        try {
            $response = Http::timeout(10)->post('http://localhost:5000/cluster', [
                'student_ids' => $studentIds,
                'course_id' => $courseId,
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

    public function showUsers(Request $request) {
        $users = User::all();
        $usersCount = $users->count();
        $studentsCount = $users->where('role', 'student')->count();
        $professorsCount = $users->where('role', 'professor')->count();
        $adminsCount = $users->where('role', 'admin')->count();

        $studentDistributionByYear = [
            $users->where('year', 'freshman')->count(),
            $users->where('year', 'sophomore')->count(),
            $users->where('year', 'junior')->count(),
            $users->where('year', 'senior')->count(),
        ];

        $userRegistrationTrend = [
            Enrollment::where('enrolled_at', '2022-01-01')->count(),
            Enrollment::where('enrolled_at', '2022-08-01')->count(),
            Enrollment::where('enrolled_at', '2023-01-01')->count(),
            Enrollment::where('enrolled_at', '2023-08-01')->count(),
            Enrollment::where('enrolled_at', '2024-01-01')->count(),
            Enrollment::where('enrolled_at', '2024-08-01')->count(),
            Enrollment::where('enrolled_at', '2025-01-01')->count(),
            Enrollment::where('enrolled_at', '2025-08-01')->count()
        ];

        $role = $request->input('role', 'all');
        if($role && $role != 'all') {
            $users = $users->filter(function($user) use($role) {
                return $user->role == $role;
            });
        }

        $search = $request->input('search', null);
        if($search) {
            $users = $users->filter(function($user) use($search) {
                return stripos($user->id, $search) !== false || stripos($user->name, $search) !== false;
            });
        }

        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $users->forPage(request()->get('page', 1), 50),
            $users->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.users', compact(
            'users', 
            'usersCount', 
            'studentsCount', 
            'professorsCount', 
            'adminsCount', 
            'studentDistributionByYear',
            'userRegistrationTrend',
        ));
    }

    public function deleteUser($userId) {
        $user = User::findOrFail($userId);
        $name = $user->name;
        $user->delete();
        return redirect()->back()->with("success", "user $name deleted successfully!");
    }

    public function updateUser(Request $request, $userId) {
        $user = User::findOrFail($userId);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|max:255',
        ]);

        $user->update($data);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function createUser() {
        $parents = User::where('role', 'parent')->get();
        return view('admin.addUser', compact('parents'));
    }

    public function addUser(Request $request) {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:3|max:255',
            'role' => 'required|in:student,professor,parent,admin',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
        ];

        if ($request->role === 'student') {
            $rules['year'] = 'required|in:freshman,sophomore,junior,senior';
            $rules['parent_id'] = 'nullable|exists:users,id';
        } elseif ($request->role === 'professor') {
            $rules['department'] = 'required|in:General Education,Computer Science,Artificial Intelligence,Information System';
        }

        $validated = $request->validate($rules);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'birthdate' => $validated['birthdate'],
        ];    
        
        if ($request->role === 'student') {
            $userData['year'] = $validated['year'];
            $userData['parent_id'] = $validated['parent_id'];
        } elseif ($request->role === 'professor') {
            $userData['department'] = $validated['department'];
        }

        User::create($userData);

        return redirect()->back()->with('success', 'User created successfully');
    }

    public function showCourses(Request $request) {
        $courses = Course::with('professor')->withCount(['enrollments as enrollments_count' => function($query) {
            $query->whereDate('enrolled_at', '2025-08-01');
        }])->get();

        $topCourses = $courses->sortByDesc('enrollments_count')->take(5);
        $topFiveCourses = [
            'labels' => $topCourses->pluck('code')->toArray(),
            'data' => $topCourses->pluck('enrollments_count')->toArray(),
        ];

        $difficultyDistribution = [
            $courses->where('difficulty', 'easy')->count(),
            $courses->where('difficulty', 'medium')->count(),
            $courses->where('difficulty', 'hard')->count()
        ];

        $professors = User::where('role', 'professor')->orderBy('name')->get();
        $professorsDistribution = [
            $professors->where('department', 'General Education')->count(),
            $professors->where('department', 'Computer Science')->count(),
            $professors->where('department', 'Artificial Intelligence')->count(),
            $professors->where('department', 'Information System')->count(),
        ];

        $generalCourses = $courses->where('department', 'General Education')->count();
        $CScourses = $courses->where('department', 'Computer Science')->count();
        $AIcourses = $courses->where('department', 'Artificial Intelligence')->count();
        $IScourses = $courses->where('department', 'Information System')->count();

        $department = $request->input('department', 'all');
        if($department && $department != 'all') {
            $courses = $courses->filter(function($course) use($department) {
                return $course->department == $department;
            });
        }

        $search = $request->input('search', null);
        if($search) {
            $courses = $courses->filter(function($course) use($search) {
                return stripos($course->code, $search) !== false || stripos($course->name, $search) !== false;
            });
        }

        $courses = new \Illuminate\Pagination\LengthAwarePaginator(
            $courses->forPage(request()->get('page', 1), 50),
            $courses->count(),
            50,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.courses', compact(
            'courses', 
            'professors',
            'generalCourses',
            'CScourses',
            'AIcourses',
            'IScourses',
            'topFiveCourses',
            'difficultyDistribution',
            'professorsDistribution'
        ));
    }

    public function createCourse() {
        $professors = User::where('role', 'professor')->orderBy('name')->get();
        return view('admin.addCourse', compact('professors'));
    }

    public function addCourse(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'description' => 'required|string',
            'department' => 'required|in:General Education,Computer Science,Artificial Intelligence,Information System',
            'credit_hours' => 'required|integer|min:1',
            'semester' => 'required|in:first,second',
            'type' => 'required|in:compulsory,elective',
            'difficulty' => 'required|in:easy,medium,hard',
            'professor_id' => 'required|exists:users,id',
        ]);

        Course::create($validated);

        return redirect()->back()->with('success', 'Course created successfully!');
    }

    public function updateCourse(Request $request, $courseId) {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $courseId,
            'description' => 'required|string',
            'department' => 'required|in:General Education,Computer Science,Artificial Intelligence,Information System',
            'credit_hours' => 'required|integer|min:1',
            'semester' => 'required|in:first,second',
            'type' => 'required|in:compulsory,elective',
            'difficulty' => 'required|in:easy,medium,hard',
            'professor_id' => 'required|exists:users,id',
        ]);

        $course->update($validated);

        return redirect()->back()->with('success', 'Course updated successfully!');
    }

    public function deleteCourse($courseId) {
        $course = Course::findOrFail($courseId);
        $name = $course->name;
        $course->delete();
        return redirect()->back()->with('success', "Course $name deleted successfully!");
    }

    public function profile() {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function userProfile($userId) {
        $user = User::findOrFail($userId);
        $totalCredits = 0;
        $maxCredits = 0;
        $gpa = 0;
        $gradePoints = [
            'A+' => 4.0, 'A'  => 4.8, 'A-' => 3.7,
            'B+' => 3.3, 'B'  => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C'  => 2.0, 'C-' => 1.7,
            'D+' => 1.3, 'D'  => 1.0, 'D-' => 0.7,
            'F'  => 0.0,
        ];
        if($user->role == 'student') {
            $grades = $user->grades()->with('course')->get();
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
        }

        return view('admin.userProfile', compact('user', 'gpa', 'totalCredits', 'maxCredits'));
    }
}
