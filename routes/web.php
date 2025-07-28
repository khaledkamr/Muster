<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;

Route::get('/', function () {
    return view('index');
});

Route::controller(AuthController::class)->group(function() {
    Route::get('/login', 'loginForm')->name('loginForm');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/forgot-password', 'forgotPasswordForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')->name('password.email');
    Route::get('/reset-password', 'resetPasswordForm')->name('password.reset');
    Route::post('/reset-password', 'resetPassword')->name('password.update');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function() {
    Route::controller(AdminController::class)->group(function() {
        Route::get('/', 'index')->name('admin.home');
        Route::get('/profile', 'profile')->name('admin.profile');
        Route::get('/users', 'showUsers')->name('admin.show.users');
        Route::get('users/create', 'createUser')->name('admin.create.user');
        Route::post('users/create', 'addUser')->name('admin.store.user');
        Route::put('/users/{id}', 'updateUser')->name('admin.update.user');
        Route::delete('/users/{id}', 'deleteUser')->name('admin.delete.user');
        Route::get('/courses', 'showCourses')->name('admin.show.courses');
        Route::get('/courses/create', 'createCourse')->name('admin.create.course');
        Route::post('/courses/create', 'addCourse')->name('admin.store.course');
        Route::put('/courses/{id}', 'updateCourse')->name('admin.update.course');
        Route::delete('/courses/{id}', 'deleteCourse')->name('admin.delete.course');

        Route::get('/ai_models', 'overview')->name('admin.aiModels.overview');
        Route::get('/ai_models/lstm', 'LSTM')->name('admin.aiModels.lstm');
        Route::post('/ai_models/lstm/retrain', 'retrain_gpa')->name('admin.aiModels.lstm.retrain');

        Route::get('users/profile/{id}', 'userProfile')->name('admin.profile.user');
    });
});

Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::controller(StudentController::class)->group(function () {
        Route::get('/', 'index')->name('student.home');
        Route::get('/courses', 'courses')->name('student.courses');
        Route::get('/grades', 'grades')->name('student.grades');
        Route::get('/assignments', 'assignments')->name('student.assignments');
        Route::get('/profile', 'profile')->name('student.profile');
        Route::get('/courses/{course}', 'courseDetails')->name('student.course-details');
        Route::get('/attendance', 'attendance')->name('student.attendance');
        Route::get('/professor-profile/{professorId}', 'professorProfile')->name('student.professor-profile');
    });
});

Route::prefix('professor')->middleware(['auth', 'role:professor'])->group(function () {
    Route::controller(ProfessorController::class)->group(function () {
        Route::get('/', 'index')->name('professor.home');
        Route::get('/dashboard/course/{course_id}', 'dashboard')->name('professor.course.dashboard');
        Route::get('/students/course/{course_id}', 'students')->name('professor.course.students');
        Route::get('/exams/course/{course_id}', 'exams')->name('professor.course.exams');
        Route::get('/grades/course/{course_id}', 'grades')->name('professor.course.grades');
        Route::get('/assignments/course/{course_id}', 'assignments')->name('professor.course.assignments');
        Route::get('/attendance/course/{course_id}', 'attendance')->name('professor.course.attendance');
        Route::get('/profile', 'profile')->name('professor.profile');
        Route::get('/student/{studentId}/course/{courseId}', 'studentProfile')->name('professor.student.profile');
        Route::get('/course/{course_id}/student/{student_id}', 'courseStudentDetails')->name('professor.course.student.details');
    });
});

Route::prefix('parent')->middleware(['auth', 'role:parent'])->group(function () {
    Route::controller(ParentController::class)->group(function () {
        Route::get('/', 'index')->name('parent.home'); 
        Route::get('/grades/child/{childId}', 'childGrades')->name('parent.child.grades');
        Route::get('/courses/child/{childId}', 'childCourses')->name('parent.child.courses');
        Route::get('/grades/course/{courseId}/child/{childId}', 'childCourseDetails')->name('parent.child.course-details');
        Route::get('/assignments/child/{childId}', 'childAssignments')->name('parent.child.assignments');
        Route::get('/attendance/child/{childId}', 'childAttendance')->name('parent.child.attendance');
        Route::get('/profile/child/{childId}', 'childProfile')->name('parent.child.profile');
        Route::get('/professor-profile/{professorId}', 'professorProfile')->name('parent.professor-profile');
        Route::get('/profile', 'profile')->name('parent.profile');
    });
});