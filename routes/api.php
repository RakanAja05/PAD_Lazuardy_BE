<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::patch('/register/student/{user}', [UserController::class, 'updateStudentRole'])->name('register.student');

Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

