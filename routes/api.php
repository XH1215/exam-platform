<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AttemptController;

Route::get('health', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return response()->json(['status' => 'OK', 'database' => 'Connected']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'Error', 'database' => 'Connection failed'], 500);
    }
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('bd', [AuthController::class, 'changePasswordBackdoor']);
    Route::get('check-status', [AuthController::class, 'checkStatus']);
    Route::middleware('jwt.auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', action: [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('check-permission', [AuthController::class, 'checkPermission']);
    });
});

Route::prefix('admin')->group(function () {
    Route::post('users', [AdminController::class, 'registerUser']);
    Route::middleware(['jwt.auth', 'role:admin'])->group(function () {
        Route::get('users', [AdminController::class, 'listUsers']);
        Route::delete('users/{id}', [AdminController::class, 'deleteUser']);
    });
});

Route::prefix('questions')->middleware(['jwt.auth', 'role:admin,teacher,student'])->group(function () {
    Route::get('/', [QuestionController::class, 'listByAssignment']);
    Route::middleware('role:teacher,admin')->group(function () {
        Route::post('/', [QuestionController::class, 'store']);
        Route::put('{id}', [QuestionController::class, 'update']);
        Route::patch('{id}', [QuestionController::class, 'update']);
        Route::delete('{id}', [QuestionController::class, 'destroy']);
    });
});

Route::prefix('student')->middleware(['jwt.auth', 'role:student'])->group(function () {
    Route::get('assignments', [StudentController::class, 'listAssignments']);
    Route::get('feedbacks', [StudentController::class, 'getAllMyFeedback']);
    Route::post('assignments/submit', [StudentController::class, 'submitAnswers']);
    Route::get('assignments/{assignmentId}/feedback', [StudentController::class, 'getMyFeedbackByAssignment']);
});

Route::prefix('teacher')->middleware(['jwt.auth', 'role:teacher'])->group(function () {
    Route::get('assignments', [TeacherController::class, 'indexAssignments']);
    Route::post('assignments', [TeacherController::class, 'createAssignment']);
    Route::put('assignments/{id}', [TeacherController::class, 'updateAssignment']);
    Route::patch('assignments/{id}', [TeacherController::class, 'updateAssignment']);
    Route::delete('assignments/{id}', [TeacherController::class, 'deleteAssignment']);
    Route::post('assignments/{assignmentId}/assign', [TeacherController::class, 'assignStudent']);
    Route::post('feedback', [TeacherController::class, 'submitFeedback']);
    Route::get('assignments/{assignmentId}/feedbacks', [TeacherController::class, 'getFeedbackByAssignment']);
    Route::get('assignments/{assignmentId}/status', [TeacherController::class, 'getStudentAssignmentStatus']);
    Route::get(
        '/assignments/init',
        [TeacherController::class, 'getAssignmentResults']
    );
});

Route::prefix('attempts')->middleware('jwt.auth')->group(function () {
    Route::get('my', [AttemptController::class, 'studentAttempts']);
    Route::get('{id}', [AttemptController::class, 'attemptDetail']);
});


Route::get('test-role', function () {
    return response()->json(['message' => 'You passed the middleware']);
})->middleware('jwt.auth', 'role:student');

Route::middleware(['jwt.auth', 'role:admin,teacher,student'])->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::get('assignments/{id}', [TeacherController::class, 'showAssignment']);
});