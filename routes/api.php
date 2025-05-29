<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuizController;

// Public route for login
Route::post('login', [AuthController::class, 'login']);

// Group routes under /admin prefix, protected by JWT auth and role=admin
Route::prefix('admin')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('users', [AdminController::class, 'listUsers']);
    Route::post('users', [AdminController::class, 'createUser']);
    Route::get('classes', [AdminController::class, 'listClasses']);
    Route::post('classes', [AdminController::class, 'createClass']);
});

// Group routes under /teacher prefix, protected by JWT auth and role=teacher
Route::prefix('teacher')->middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('assignments', [TeacherController::class, 'indexAssignments']);
    Route::post('assignments', [TeacherController::class, 'createAssignment']);
    Route::get('games', [TeacherController::class, 'indexGames']);
    Route::post('games', [TeacherController::class, 'createGame']);
});

// Group routes under /student prefix, protected by JWT auth and role=student
Route::prefix('student')->middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('assignments', [StudentController::class, 'indexAssignments']);
    Route::post('assignments', [StudentController::class, 'submitAssignment']);
    Route::get('profile', [StudentController::class, 'profile']);
    Route::post('quiz', [QuizController::class, 'submit']);
});
