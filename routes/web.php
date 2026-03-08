<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\SocialAuth\SocialAuthController;

// Google OAuth Routes (butuh session untuk state verification)
// Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
// Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/', function () {
    return ['message' => 'API Backend Laravel - PAD Lazuardy'];
});

Route::get('/__debug/db', function () {
    return [
        'database' => Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
        'users_has_role' => Illuminate\Support\Facades\Schema::hasColumn('users', 'role'),
    ];
});

// Social Auth Routes (support multiple providers: google, facebook, dll)
Route::name('social.')->group(function(){
    Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('callback');
});
