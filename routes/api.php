<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TutorApplicationController;
use App\Models\Review;
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
use App\Http\Controllers\TutorVerifyController;
use App\Http\Controllers\TutorSalaryController;

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

    // === ROLE-BASED ROUTES ===
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

    // Admin-only routes (gunakan middleware role yang sudah ada)
    Route::middleware('role:admin')->group(function () {
        // Data untuk registrasi tutor (dropdown / form options) - hanya admin
        Route::get('/verify/tutor', [TutorVerifyController::class, 'index']);
        Route::patch('/verify/tutor/approve', [TutorVerifyController::class, 'approve']);
        Route::patch('/verify/tutor/reject', [TutorVerifyController::class, 'reject']);

        // Tutor Salary Management
        Route::get('/admin/tutor-salary', [TutorSalaryController::class, 'index']);
        Route::get('/admin/tutor-salary/{userId}', [TutorSalaryController::class, 'show']);
        Route::post('/admin/tutor-salary/{userId}/confirm', [TutorSalaryController::class, 'confirmPayment']);
        Route::post('/admin/tutor-salary/confirm-batch', [TutorSalaryController::class, 'confirmBatchPayment']);
        Route::post('/admin/tutor-salary/{userId}/confirm-with-invoice', [TutorSalaryController::class, 'confirmPaymentWithInvoice']);
        Route::get('/admin/tutor-salary/pending-payment', [TutorSalaryController::class, 'getPendingPayment']);
        Route::get('/admin/tutor/verification-pending', [TutorSalaryController::class, 'getVerificationPending']);
        Route::get('/admin/tutor-salary/{userId}/history', [TutorSalaryController::class, 'getSalaryHistory']);
    });

});

// === GUEST ROUTES (belum login) ===
Route::middleware('guest')->group(function () {
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
Route::post('/auth/{provider}/mobile', [SocialAuthController::class, 'mobileLogin']);

// Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
// Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
