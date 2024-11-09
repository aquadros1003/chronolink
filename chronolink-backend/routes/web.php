<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('route.prefix')], function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
