<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;




Route::resource('messages', MessageController::class);

///////////////////////////////////////////////////////////////

Route::get('/groups', [GroupController::class, 'index']);

Route::get('/groups/{id}', [GroupController::class, 'show']);

///////////////////////////////////////////////////////////////

Route::get('/users', [UserController::class, 'index']);

Route::get('/users/{id}', [UserController::class, 'show']);

///////////////////////////////////////////////////////////////

Route::get('/test', function () {
    return response()->json(['message' => 'This is a test route']);
});







