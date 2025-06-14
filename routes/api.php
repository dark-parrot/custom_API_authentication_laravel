<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;

// Public API routes (no authentication required)
Route::post('/register', [authController::class, 'register']);
Route::post('/login', [authController::class, 'login']);

// Protected API routes (authentication required)
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [authController::class, 'logout']);
    Route::get('/user', [authController::class, 'user']);
});