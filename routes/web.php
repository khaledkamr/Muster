<?php

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
});

Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::controller(StudentController::class)->group(function () {
        Route::get('/', 'index')->name('student.home');
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
        Route::get('/students/course/{course_id}', 'students')->name('professor.course.students');
        Route::get('/grades/course/{course_id}', 'grades')->name('professor.course.grades');
        Route::get('/assignments/course/{course_id}', 'assignments')->name('professor.course.assignments');
        Route::get('/attendance/course/{course_id}', 'attendance')->name('professor.course.attendance');
        Route::get('/profile', 'profile')->name('professor.profile');
        Route::get('/student/{studentId}/course/{courseId}', 'studentProfile')->name('professor.student.profile');
    });
});

Route::prefix('parent')->middleware(['auth', 'role:parent'])->group(function () {
    Route::controller(ParentController::class)->group(function () {
        Route::get('/', 'index')->name('parent.home'); 
        Route::get('/grades/child/{childId}', 'childGrades')->name('parent.child.grades');
        Route::get('/grades/course/{courseId}/child/{childId}', 'childCourseDetails')->name('parent.child.course-details');
        Route::get('/assignments/child/{childId}', 'childAssignments')->name('parent.child.assignments');
        Route::get('/attendance/child/{childId}', 'childAttendance')->name('parent.child.attendance');
        Route::get('/profile/child/{childId}', 'childProfile')->name('parent.child.profile');
        Route::get('/professor-profile/{professorId}', 'professorProfile')->name('parent.professor-profile');
        Route::get('/profile', 'profile')->name('parent.profile');
    });
});