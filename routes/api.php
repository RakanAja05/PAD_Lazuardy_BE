<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;

// === REGISTER & LOGIN BIASA ===
Route::post('/login', [LoginController::class, 'login']);

// === LOGIN GOOGLE UNTUK MOBILE ===
Route::post('/auth/google/mobile', [GoogleController::class, 'mobileLogin']);

// === PROTECTED ROUTES (wajib login) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Registration and Authentication Routes
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'initiateRegister'])->name('register.initiate');
    Route::patch('/register/verify', [AuthController::class, 'verifyEmail'])->name('register.verify-email');
    Route::patch('/register/resend-otp', [AuthController::class, 'resendOtp'])->name('register.resend-otp');
    
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
    Route::patch('/forgot-password/verify', [AuthController::class, 'verifyForgotPassword'])->name('password.verify-otp');
    Route::patch('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});


Route::middleware('auth:sanctum')->group(function(){
    Route::patch('/register/student', [UserController::class, 'updateStudentRole'])->name('register.student');
    Route::patch('/register/tutor', [UserController::class, 'updateTutorRole'])->name('register.tutor');
}); 

Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
