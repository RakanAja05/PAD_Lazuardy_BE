<?php

use App\Http\Controllers\SocialAuth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/{provider}/mobile', [SocialAuthController::class, 'mobileLogin']);
