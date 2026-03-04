<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/auth/google/complete', [GoogleController::class, 'completeGoogleRegistration'])->name('google.complete');
    Route::post('/register', [AuthController::class, 'sendRegisterOtp'])->name('register.sendOtp');
    Route::patch('/register/verify', [AuthController::class, 'verifyRegisterOtp'])->name('register.verify-otp');
    Route::patch('/register/resend-otp', [AuthController::class, 'resendRegisterOtp'])->name('register.resend-otp');
    Route::patch('/register/student', [AuthController::class, 'storeStudentRegister'])->name('register.student');
    Route::patch('/register/tutor', [AuthController::class, 'storeTutorRegister'])->name('register.tutor');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
    Route::patch('/forgot-password/verify', [AuthController::class, 'verifyForgotPassword'])->name('password.verify-otp');
    Route::patch('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);
});
