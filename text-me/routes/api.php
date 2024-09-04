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

Route::post('/groups', [GroupController::class, 'store']);

Route::put('/groups/{id}', [GroupController::class, 'update']);

Route::delete('/groups/{id}', [GroupController::class, 'destroy']);

///////////////////////////////////////////////////////////////

Route::get('/users', [UserController::class, 'index']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::post('/users', [UserController::class, 'store']);

Route::put('/users/{id}', [UserController::class, 'update']);

Route::delete('/users/{id}', [UserController::class, 'destroy']);

///////////////////////////////////////////////////////////////

Route::get('/test', function () {
    return response()->json(['message' => 'This is a test route']);
});







