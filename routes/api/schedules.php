<?php

use App\Http\Controllers\Schedules\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:tutor')->group(function () {
        Route::get('/tutor/schedule', [ScheduleController::class, 'indexTutor']);
    });

    Route::middleware('role:student')->group(function () {
        Route::get('/student/schedule', [ScheduleController::class, 'indexStudent']);
    });
});
