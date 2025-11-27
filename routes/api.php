<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\TutorApplicationController;
use App\Models\Review;

// Authentication
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'sendRegisterOtp'])->name('register.sendOtp');
    Route::patch('/register/verify', [AuthController::class, 'verifyRegisterOtp'])->name('register.verify-otp');
    Route::patch('/register/resend-otp', [AuthController::class, 'resendRegisterOtp'])->name('register.resend-otp');
    Route::patch('/register/student', [AuthController::class, 'storeStudentRegister'])->name('register.student');
    Route::patch('/register/tutor', [AuthController::class, 'storeTutorRegister'])->name('register.tutor');
    
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
        // tutor apply
        Route::get('/tutor/apply', [TutorApplicationController::class, 'index']);
        Route::post('/tutor/apply', [TutorApplicationController::class, 'store']);
        Route::patch('/tutor/lesson-formulir', [ProfileController::class, 'updateTutorLessonMethod']);

        // profile
        Route::get('/tutor/profile', [ProfileController::class, 'showTutorProfile']);
        Route::get('/tutor/profile/edit', [ProfileController::class, 'showTutorProfile']);
        Route::patch('/tutor/profile', [ProfileController::class, 'updateTutorProfile']);

        // Presence
        Route::get('/tutor/presence', [PresenceController::class, 'index']);
        Route::post('/tutor/presence', [PresenceController::class, 'create']);

        // Schedule
        Route::get('/tutor/schedule', [ScheduleController::class, 'indexTutor']);
    });

    Route::middleware('role:student')->group(function (){
        // profile
        Route::get('/student/profile', [ProfileController::class, 'showStudentProfile']);
        Route::get('/student/profile/edit', [ProfileController::class, 'showStudentProfile']);
        Route::patch('/student/profile', [ProfileController::class, 'updateStudentProfile']);

        // order & payment
        Route::get('/package/order', [PaymentController::class, 'showPaymentPackage']);
        Route::post('/package/order', [PaymentController::class, 'storeOrderPackage']);
        Route::post('/package/payment', [PaymentController::class, 'uploadPaymentFile'])->name('payment.upload');
        Route::get('/payment/history', [PaymentController::class, 'showHistory']);
        Route::get('/payment/history/detail', [PaymentController::class, 'showDetail']);

        // Schedule
        Route::get('/student/schedule', [ScheduleController::class, 'indexStudent']);

        // review
        Route::get('/student/review', [ReviewController::class, 'index']);
        Route::get('/student/review/{tutor_id}', [ReviewController::class, 'show']);
        Route::post('/student/review', [ReviewController::class, 'storeOrUpdate']);
        Route::patch('/student/review', [ReviewController::class, 'storeOrUpdate']);
    });

    Route::middleware('role:admin')->group(function(){
        Route::get('/admin/student', [StudentManagementController::class, 'index']);
        Route::get('/admin/student/{id}', [StudentManagementController::class, 'show']);
        Route::patch('/admin/student/{id}/accept', [StudentManagementController::class, 'accept']);
        Route::patch('/admin/student/{id}/reject', [StudentManagementController::class, 'reject']);
    });
    
});



// Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
// Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
