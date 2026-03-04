<?php

use App\Http\Controllers\Admin\ReportAndAnalyticController;
use App\Http\Controllers\Admin\SalaryPaymentHistoryController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\TutorSalaryController;
use App\Http\Controllers\Admin\TutorVerifyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/verify/tutor', [TutorVerifyController::class, 'index']);
        Route::patch('/verify/tutor/approve', [TutorVerifyController::class, 'approve']);
        Route::patch('/verify/tutor/reject', [TutorVerifyController::class, 'reject']);

        Route::get('/admin/tutor-salary', [TutorSalaryController::class, 'index']);
        Route::get('/admin/tutor-salary/{userId}', [TutorSalaryController::class, 'show']);
        Route::post('/admin/tutor-salary/{userId}/confirm', [TutorSalaryController::class, 'confirmPayment']);
        Route::post('/admin/tutor-salary/confirm-batch', [TutorSalaryController::class, 'confirmBatchPayment']);
        Route::post('/admin/tutor-salary/{userId}/confirm-with-invoice', [TutorSalaryController::class, 'confirmPaymentWithInvoice']);
        Route::get('/admin/tutor-salary/pending-payment', [TutorSalaryController::class, 'getPendingPayment']);
        Route::get('/admin/tutor/verification-pending', [TutorSalaryController::class, 'getVerificationPending']);
        Route::get('/admin/tutor-salary/{userId}/history', [TutorSalaryController::class, 'getSalaryHistory']);
        Route::get('/admin/tutor-salary/{userId}/payment-history', [SalaryPaymentHistoryController::class, 'getPaymentHistory']);

        Route::get('/admin/student', [StudentManagementController::class, 'index']);
        Route::get('/admin/student/{id}', [StudentManagementController::class, 'show']);
        Route::patch('/admin/student/{id}/accept', [StudentManagementController::class, 'accept']);
        Route::patch('/admin/student/{id}/reject', [StudentManagementController::class, 'reject']);

        Route::get('/admin/analytic', [ReportAndAnalyticController::class, 'index']);
    });
});
