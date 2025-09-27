<?php

use App\RatingOption;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dd(array_column(RatingOption::cases(), 'value'));

    return view('welcome');
});
