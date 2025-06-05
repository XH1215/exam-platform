<?php

use Illuminate\Support\Facades\Route;
Route::view('/welcome', 'welcome')->name('welcome');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
Route::view('/editProfile', 'edit_profile')->name('editProfile');

Route::get('/quizHome', function () {
    return view('student.quizHome');
})->name('quizHome');

Route::get('/assignment/{assignment_id}', function ($assignment_id) {
    return view('student.quiz', compact('assignment_id'));
})->name('assignment.show');

Route::view('/accManage', 'admin.accManage')->name('accManage');
Route::view('/feedback', 'student.feedback')->name('feedback');
Route::view('/quizManage', 'teacher.quizManage')->name('quizManage');
Route::view('/viewQuestions/{assignmentId}', 'teacher.viewQuestions')
    ->where('assignmentId', '[0-9]+')
    ->name('viewQuestions');

