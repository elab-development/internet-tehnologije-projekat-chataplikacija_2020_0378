<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('online', function ($user) {
    return $user; //ako vrati true znaci da je korisnik autentifikovan i povezao se na kanal pa nam vraca status 200
});
