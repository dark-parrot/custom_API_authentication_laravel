<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;

Route::get('/', function () {
    return view('register');
});

// Web routes for serving views
Route::get('/register', function() { return view('register'); })->name('register');
Route::get('/login', function() { return view('login'); })->name('login');
Route::get('/dashboard', function() { return view('dashboard'); })->name('dashboard');
