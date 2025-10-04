<?php

use App\Enums\RatingOption;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});
