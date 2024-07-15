<?php


use Illuminate\Support\Facades\Broadcast;
use App\Http\Resources\UserResource;

Broadcast::channel('online', function ($user) {
    return $user ? new UserResource($user) : null; //ako vrati true znaci da je korisnik autentifikovan i povezao se na kanal pa nam vraca status 200
});
