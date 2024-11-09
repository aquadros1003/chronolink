<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('route.prefix').'/api'], function () {
    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
        Route::POST('register', [AuthController::class, 'register']);
        Route::POST('login', [AuthController::class, 'login']);
        Route::POST('logout', [AuthController::class, 'logout']);
        Route::POST('refresh', [AuthController::class, 'refresh']);
        Route::GET('me', [AuthController::class, 'me']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::GET('timelines', [TimelineController::class, 'userTimelines']);
        Route::GET('timelines/{timeline}', [TimelineController::class, 'show']);
        Route::POST('create-timeline', [TimelineController::class, 'store']);
        Route::PUT('update-timeline/{timeline}', [TimelineController::class, 'update']);
        Route::DELETE('delete-timeline/{timeline}', [TimelineController::class, 'destroy']);
        Route::POST('assign-user/{timeline}', [TimelineController::class, 'assignUser']);
        Route::GET('timelines/{timeline}', [TimelineController::class, 'show']);
        Route::GET('events/{timeline}', [EventController::class, 'index']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::GET('labels/{timeline}', [LabelController::class, 'index']);
        Route::POST('create-label', [LabelController::class, 'store']);
        Route::PUT('update-label/{label}', [LabelController::class, 'update']);
        Route::DELETE('delete-label/{label}', [LabelController::class, 'destroy']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::GET('permissions', [PermissionController::class, 'index']);
        Route::PUT('timelines/{timeline}/update-user-permissions', [PermissionController::class, 'updateUserTimelinePemissions']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::GET('users/{search}', [UserController::class, 'userEmails']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::POST('create-event', [EventController::class, 'store']);
        Route::PUT('update-event/{event}', [EventController::class, 'update']);
        Route::DELETE('delete-event/{event}', [EventController::class, 'destroy']);
        Route::GET('event/{event}', [EventController::class, 'show']);
    });
});
