<?php

use App\Http\Controllers\Students\ProfileController;
use App\Http\Controllers\Students\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:student')->group(function () {
        Route::get('/student/profile', [ProfileController::class, 'showStudentProfile']);
        Route::get('/student/profile/edit', [ProfileController::class, 'showStudentProfile']);
        Route::patch('/student/profile', [ProfileController::class, 'updateStudentProfile']);

        Route::get('/student/review', [ReviewController::class, 'index']);
        Route::get('/student/review/{tutor_id}', [ReviewController::class, 'show']);
        Route::post('/student/review', [ReviewController::class, 'storeOrUpdate']);
        Route::patch('/student/review', [ReviewController::class, 'storeOrUpdate']);
    });
});
