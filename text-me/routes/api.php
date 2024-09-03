<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;



Route::get('/messages/{id}', [MessageController::class, 'show']);

Route::get('/messages', [MessageController::class, 'index']);

///////////////////////////////////////////////////////////////

Route::get('/groups', [GroupController::class, 'index']);

Route::patch('/groups/update/{group}', [GroupController::class, 'update']);

///////////////////////////////////////////////////////////////


Route::get('/users', [UserController::class, 'index']);



Route::get('/test', function () {
    return response()->json(['message' => 'This is a test route']);
});




