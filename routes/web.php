<?php

use App\Enums\RatingOption;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});
// Route::name('social.')->group(function(){
//     Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('login');
//     Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
// });