<?php

use App\Http\Controllers\AuthController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('index');
});

Route::get('/student_dashboard', function() {
    $user = FacadesAuth::user();
    return view('student.index', compact('user'));
})->name('student.index');

Route::get('/professor_dashboard', function() {
    $user = FacadesAuth::user();
    return view('professor.index', compact('user'));
})->name('professor.index');

Route::get('/parent_dashboard', function() {
    $user = FacadesAuth::user();
    $children = $user->children;
    return view('parent.index', compact('user', 'children'));
})->name('parent.index');

Route::controller(AuthController::class)->group(function() {
    Route::get('/login', 'loginForm')->name('loginForm');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student', [StudentController::class, 'index'])->name('student.home');
    Route::get('/student/courses', [StudentController::class, 'courses'])->name('student.courses');
    Route::get('/student/assignments', [StudentController::class, 'assignments'])->name('student.assignments');
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/logout', [StudentController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:professor'])->group(function () {
    Route::get('/professor', [ProfessorController::class, 'index'])->name('professor.home');
    Route::get('/professor/courses', [ProfessorController::class, 'courses'])->name('professor.courses');
    Route::get('/professor/assignments', [ProfessorController::class, 'assignments'])->name('professor.assignments');
    Route::get('/professor/profile', [ProfessorController::class, 'profile'])->name('professor.profile');
    Route::post('/logout', [ProfessorController::class, 'logout'])->name('logout');
});