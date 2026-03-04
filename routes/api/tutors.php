<?php

use App\Http\Controllers\Students\ProfileController;
use App\Http\Controllers\Tutors\FindTutorController;
use App\Http\Controllers\Tutors\PresenceController;
use App\Http\Controllers\Tutors\TutorApplicationController;
use App\Http\Controllers\Tutors\TutorProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/find-tutor', [FindTutorController::class, 'search']);
    Route::get('/find-tutor/{id}', [FindTutorController::class, 'show']);

    Route::get('/tutor-profile/{id}', [TutorProfileController::class, 'show']);
    Route::get('/tutor-profile/{id}/available-slots', [TutorProfileController::class, 'availableSlots']);

    Route::middleware('role:tutor')->group(function () {
        Route::get('/tutor/apply', [TutorApplicationController::class, 'index']);
        Route::post('/tutor/apply', [TutorApplicationController::class, 'store']);
        Route::patch('/tutor/lesson-formulir', [ProfileController::class, 'updateTutorLessonMethod']);

        Route::get('/tutor/profile', [ProfileController::class, 'showTutorProfile']);
        Route::get('/tutor/profile/edit', [ProfileController::class, 'showTutorProfile']);
        Route::patch('/tutor/profile', [ProfileController::class, 'updateTutorProfile']);

        Route::get('/tutor/presence', [PresenceController::class, 'index']);
        Route::post('/tutor/presence', [PresenceController::class, 'create']);
    });
});
