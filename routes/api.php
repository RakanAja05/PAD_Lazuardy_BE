<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'initiateRegister'])->name('register.initiate');
Route::patch('/register/verify', [AuthController::class, 'verifyEmail'])->name('register.verify-email');
Route::post('/register/otp/send', [VerificationController::class, 'sendOtp'])->name('register.otp.send');


Route::middleware('auth:sanctum')->group(function(){
    Route::patch('/register/student', [UserController::class, 'updateStudentRole'])->name('register.student');
    Route::patch('/register/tutor', [UserController::class, 'updateTutorRole'])->name('register.tutor');

    Route::prefix('otp')->group(function () {
        Route::post('/send', [VerificationController::class, 'sendOtp'])->name('otp.send');
        Route::patch('/verify', [VerificationController::class, 'verifyOtp'])->name('otp.verify');
        Route::patch('/resend', [VerificationController::class, 'resendOtp'])->name('otp.resend');
    });
}); 

Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);