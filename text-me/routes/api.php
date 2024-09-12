<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//////////////////////////////////////////////////////////////

// Route::middleware('auth:sanctum')->group(function () {
//     Route::resource('messages', MessageController::class);
// });

Route::resource('messages', MessageController::class)->middleware('auth:sanctum');

///////////////////////////////////////////////////////////////

Route::get('/groups', [GroupController::class, 'index']);

Route::get('/groups/{id}', [GroupController::class, 'show']);

Route::post('/groups', [GroupController::class, 'store'])->middleware('auth:sanctum');

Route::put('/groups/{id}', [GroupController::class, 'update'])->middleware('auth:sanctum');

Route::delete('/groups/{id}', [GroupController::class, 'destroy'])->middleware('auth:sanctum');

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


//////////////////////////////////////////////////////////////////
use Illuminate\Support\Facades\Http;

Route::get('/giphy-search', function (Request $request) {
    $query = $request->query('q'); // Pretraga pojam iz zahteva
    $apiKey = "dlMeoqlKVcKe1CIr9Z1kjCjkk87YIVIE"; 

    $response = Http::get("https://api.giphy.com/v1/gifs/search", [
        'api_key' => $apiKey,
        'q' => $query,
        'limit' => 10, // Broj gifova
    ]);

    return $response->json();
});





