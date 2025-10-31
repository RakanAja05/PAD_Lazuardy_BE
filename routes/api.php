<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TutorDashboardController;

// === REGISTER & LOGIN BIASA ===
Route::post('/login', [LoginController::class, 'login']);
                    
// === LOGIN GOOGLE - Complete Registration (API) ===
Route::post('/auth/google/complete', [GoogleController::class, 'completeGoogleRegistration'])->name('google.complete');

// === PROTECTED ROUTES (wajib login) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);
    
    // Dashboard Student
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index']);
    Route::get('/dashboard/student/summary', [StudentDashboardController::class, 'summary']);
    Route::get('/dashboard/student/recommended-tutors', [StudentDashboardController::class, 'getRecommendedTutors']);
    
    // Dashboard Tutor
    Route::get('/dashboard/tutor', [TutorDashboardController::class, 'index']);
    Route::get('/dashboard/tutor/summary', [TutorDashboardController::class, 'summary']);
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

// Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
// Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
