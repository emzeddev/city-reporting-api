<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/sendCode',     [AuthController::class, 'sendCode']);
        Route::post('/signIn',       [AuthController::class, 'signIn']);
        Route::post('/signUp',       [AuthController::class, 'signUp']);
        Route::post('/logout',       [AuthController::class, 'logout']);
        Route::get('/me',            [AuthController::class, 'get_user']);
    });
});
