<?php

use App\Enums\RatingOption;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    
    dd(RatingOption::VERY_GOOD->label());

    return view('welcome');
});
