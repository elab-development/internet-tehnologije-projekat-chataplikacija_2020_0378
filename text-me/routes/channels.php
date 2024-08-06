<?php


use Illuminate\Support\Facades\Broadcast;
use App\Http\Resources\UserResource;
use App\Models\User;

Broadcast::channel('online', function (User $user) {
    return $user ? new UserResource($user) : null; //ako vrati true znaci da je korisnik autentifikovan i povezao se na kanal pa nam vraca status 200
});

Broadcast::channel('message.user.{userId1}-{userId2}', function (User $user, int $userId1, int $userId2){
    return $user->id === $userId1 || $user->id === $userId2 ? $user : null;
});

Broadcast::channel('message.group.{groupId}', function (User $user, int $groupId){
    return $user->groups->contains('id', $groupId) ? $user : null;
});
