<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AttemptController;

Route::get('ping', fn() => response()->json(['pong' => true]));

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::prefix('admin')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('register-user', [AdminController::class, 'registerUser']);
    Route::get('users', [AdminController::class, 'listUsers']);
    Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
});

Route::prefix('teacher')->middleware(['auth:api', 'role:teacher'])->group(function () {
    Route::get('assignments', [TeacherController::class, 'indexAssignments']);
    Route::post('assignments', [TeacherController::class, 'createAssignment']);
    Route::get('assignments/{id}', [TeacherController::class, 'showAssignment']);
    Route::put('assignments/{id}', [TeacherController::class, 'updateAssignment']);
    Route::delete('assignments/{id}', [TeacherController::class, 'deleteAssignment']);

    Route::post('assignments/{id}/assign-student', [TeacherController::class, 'assignStudent']);
    Route::post('feedback', [TeacherController::class, 'submitFeedback']);
    Route::get('feedback/assignment/{assignmentId}', [TeacherController::class, 'getFeedbackByAssignment']);
});

Route::prefix('student')->middleware(['auth:api', 'role:student'])->group(function () {
    Route::get('assignments', [StudentController::class, 'listAssignments']);
    Route::get('feedback/{assignmentId}', [StudentController::class, 'getMyFeedbackByAssignment']);
});

Route::prefix('questions')->middleware(['auth:api'])->group(function () {
    Route::post('/', [QuestionController::class, 'store']);
    Route::put('/{id}', [QuestionController::class, 'update']);
    Route::delete('/{id}', [QuestionController::class, 'destroy']);
});

Route::get('assignments/{assignment_id}/questions', [QuestionController::class, 'listByAssignment'])
    ->middleware(['auth:api', 'role:student']);

Route::prefix('attempt')->middleware(['auth:api'])->group(function () {
    Route::post('submit', [AttemptController::class, 'submit']);
    Route::get('student', [AttemptController::class, 'studentAttempts']);
    Route::get('{id}', [AttemptController::class, 'attemptDetail']);
});
