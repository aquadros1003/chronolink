<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimelineController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::POST('register', [AuthController::class, 'register']);
    Route::POST('login', [AuthController::class, 'login']);
    Route::POST('logout', [AuthController::class, 'logout']);
    Route::POST('refresh', [AuthController::class, 'refresh']);
    Route::GET('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::POST('create-timeline', [TimelineController::class, 'store']);
    Route::PUT('update-timeline/{timeline}', [TimelineController::class, 'update']);
    Route::DELETE('delete-timeline/{timeline}', [TimelineController::class, 'destroy']);
});
