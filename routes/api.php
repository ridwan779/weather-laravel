<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;

Route::post('register', [UserController::class, 'postRegister']);
Route::post('login', [UserController::class, 'postLogin']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('users/{id}', [UserController::class, 'getUser']);
    Route::post('logout', [UserController::class, 'postLogout']);
});


