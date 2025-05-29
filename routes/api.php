<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuizController;

Route::get('ping', function () {
    return response()->json(['pong' => true]);
});

Route::post('login', [AuthController::class, 'login']);

Route::prefix('admin')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('users',   [AdminController::class,  'listUsers']);
    Route::post('users',  [AdminController::class,  'createUser']);
    Route::get('classes', [AdminController::class,  'listClasses']);
    Route::post('classes',[AdminController::class,  'createClass']);
});

Route::prefix('teacher')->middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('assignments', [TeacherController::class, 'indexAssignments']);
    Route::post('assignments',[TeacherController::class, 'createAssignment']);
    Route::get('games',       [TeacherController::class, 'indexGames']);
    Route::post('games',      [TeacherController::class, 'createGame']);
});

Route::prefix('student')->middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('assignments', [StudentController::class, 'indexAssignments']);
    Route::post('assignments',[StudentController::class, 'submitAssignment']);
    Route::get('profile',     [StudentController::class, 'profile']);
    Route::post('quiz',       [QuizController::class,   'submit']);
});
