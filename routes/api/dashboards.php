<?php

use App\Http\Controllers\Dashboards\StudentDashboardController;
use App\Http\Controllers\Dashboards\TutorDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/student', [StudentDashboardController::class, 'index']);
    Route::get('/dashboard/student/summary', [StudentDashboardController::class, 'summary']);
    Route::get('/dashboard/student/recommended-tutors', [StudentDashboardController::class, 'getRecommendedTutors']);

    Route::get('/dashboard/tutor', [TutorDashboardController::class, 'index']);
    Route::get('/dashboard/tutor/summary', [TutorDashboardController::class, 'summary']);
});
