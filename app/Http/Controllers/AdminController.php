<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class AdminController extends Controller
{
    public function index() {
        return view('admin.index');
    }

    public function showUsers(Request $request) {
        $users = User::all();
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

        return view('admin.users', compact('users'));
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
        $courses = Course::with('professor')->get();
        $professors = User::where('role', 'professor')->orderBy('name')->get();

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

        return view('admin.courses', compact('courses', 'professors'));
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
}
