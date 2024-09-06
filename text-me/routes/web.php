<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/test-web', function () {
    return response()->json(['message' => 'Web route test']);
});



Route::middleware(['auth', 'verified', 'active'])->group(function() {
    Route::get('/', [HomeController::class , 'home'])->name('dashboard');

    Route::get('user/{user}', [MessageController::class , 'byUser'])->name('chat.user');

    Route::get('group/{group}', [MessageController::class , 'byGroup'])->name('chat.group');

    Route::post('/message', [MessageController::class , 'store1'])->name('message.store');

    Route::delete('/message/{message}', [MessageController::class , 'destroy1'])->name('message.destroy');

    Route::get('/message/older/{message}', [MessageController::class , 'loadOlder'])->name('message.loadOlder');

    //////////////////////////////////////////////////////////////////////////////////////

    Route::post('/group', [GroupController::class, 'store1'])->name('group.store');

    Route::put('/group/{group}', [GroupController::class, 'update1'])->name('group.update');

    Route::delete('/group/{group}', [GroupController::class, 'destroy1'])->name('group.destroy');

    ///////////////////////////////////////////////////////////////////////////////////////

    Route::middleware(['admin'])->group(function(){
        Route::post('/user/change-role/{user}', [UserController::class, 'changeUserRole'])
            ->name('user.changeRole');
        Route::post('/user/block-unblock/{user}', [UserController::class, 'blockUnblock'])
            ->name('user.blockUnblock');
    });
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
