<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\SocialAuthController;

// Google OAuth Routes (butuh session untuk state verification)
// Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
// Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/', function () {
    return ['message' => 'API Backend Laravel - PAD Lazuardy'];
});

// Social Auth Routes (support multiple providers: google, facebook, dll)
Route::name('social.')->group(function(){
    Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('callback');
});