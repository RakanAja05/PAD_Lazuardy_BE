<?php

use App\Http\Controllers\StudyPackages\StudyPackageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-packages', [StudyPackageController::class, 'packages']);
    Route::get('/study-packages', [StudyPackageController::class, 'index']);
    Route::get('/study-packages/{id}', [StudyPackageController::class, 'show']);
});
