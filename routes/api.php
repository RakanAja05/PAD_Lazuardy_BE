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
use App\Http\Controllers\FindTutorController;
use App\Http\Controllers\TutorProfileController;
use App\Http\Controllers\StudyPackageController;
use App\Http\Controllers\NotificationController;

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
    
    // Find Tutor (Geospatial Search)
    Route::get('/find-tutor', [FindTutorController::class, 'search']);
    Route::get('/find-tutor/{id}', [FindTutorController::class, 'show']);
    
    // Tutor Profile (Lihat profile tutor lengkap)
    Route::get('/tutor-profile/{id}', [TutorProfileController::class, 'show']);
    Route::get('/tutor-profile/{id}/available-slots', [TutorProfileController::class, 'availableSlots']);
    
    // Study Package (Paket Belajar Student)
    Route::get('/my-packages', [StudyPackageController::class, 'packages']); // List paket yang dibeli
    Route::get('/study-packages', [StudyPackageController::class, 'index']); // Detail semua paket dengan subjects
    Route::get('/study-packages/{id}', [StudyPackageController::class, 'show']); // Detail paket tertentu
    
    // Notifications (hanya untuk Flutter users)
    Route::get('/notifications', [NotificationController::class, 'index']); // Get all notifications (paginated)
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']); // Get unread count
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']); // Mark all as read (harus sebelum {id})
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']); // Mark specific as read
    Route::delete('/notifications/read-all', [NotificationController::class, 'deleteAllRead']); // Delete all read (harus sebelum {id})
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']); // Delete specific notification
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
