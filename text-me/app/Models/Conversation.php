<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id1',
        'user_id2',
        'last_message_id',
    ];

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'last_message_id');//razgovor u jednom trenutku moze imati samo jednu poslednju poruku
    }

    public function user1()
    {
        return $this->hasOne(User::class, 'user_id1');
    }

    public function user2()
    {
        return $this->hasOne(User::class, 'user_id2');
    }

}
