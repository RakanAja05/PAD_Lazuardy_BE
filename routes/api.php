<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;

// === REGISTER & LOGIN BIASA ===
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// === LOGIN GOOGLE UNTUK MOBILE ===
Route::post('/auth/google/mobile', [GoogleController::class, 'mobileLogin']);

// === PROTECTED ROUTES (wajib login) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);
});
