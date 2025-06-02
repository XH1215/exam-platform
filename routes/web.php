<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
Route::view('/editProfile', 'edit_profile')->name('editProfile');
    