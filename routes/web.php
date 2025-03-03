<?php

use App\Http\Controllers\AuthController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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