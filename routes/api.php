<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;

// Authentication
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'initiateRegister'])->name('register.initiate');
    Route::patch('/register/verify', [AuthController::class, 'verifyEmail'])->name('register.verify-email');
    Route::patch('/register/resend-otp', [AuthController::class, 'resendOtp'])->name('register.resend-otp');
    
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
    Route::patch('/forgot-password/verify', [AuthController::class, 'verifyForgotPassword'])->name('password.verify-otp');
    Route::patch('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});

// === LOGIN GOOGLE UNTUK MOBILE ===
// Route::post('/auth/google/mobile', [GoogleController::class, 'mobileLogin']);
Route::post('/auth/{provider}/mobile', [SocialAuthController::class, 'mobileLogin']);

// === Route Wajib Login ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::middleware('role:tutor')->group(function () {
        Route::patch('/register/tutor', [UserController::class, 'updateTutorRegister'])->name('register.tutor');
        Route::get('/tutor/profile', [ProfileController::class, 'showTutorProfile']);
        Route::get('/tutor/profile/edit', [ProfileController::class, 'showTutorProfile']);
        Route::patch('/tutor/profile', [ProfileController::class, 'updateTutorProfile']);
    });

    Route::middleware('role:student')->group(function (){
        Route::patch('/register/student', [UserController::class, 'updateStudentRegister'])->name('register.student');
        Route::get('/student/profile', [ProfileController::class, 'showStudentProfile']);
        Route::get('/student/profile/edit', [ProfileController::class, 'showStudentProfile']);
        Route::patch('/student/profile', [ProfileController::class, 'updateStudentProfile']);
        Route::get('/package/order', [PaymentController::class, 'showPaymentPackage']);
        Route::post('/package/order', [PaymentController::class, 'storeOrderPackage']);
        Route::post('/package/payment', [PaymentController::class, 'uploadPaymentFile'])->name('payment.upload');
        Route::get('/payment/history', [PaymentController::class, 'showHistory']);
        Route::get('/payment/history/detail', [PaymentController::class, 'showDetail']);
    });
    
});



// Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
// Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
