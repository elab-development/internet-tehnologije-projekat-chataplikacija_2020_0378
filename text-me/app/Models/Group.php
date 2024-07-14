<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'last_message_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users');//grupa moze imati vise korisnika
    }

    public function messages()
    {
        return $this->hasMany(Message::class);//mozeimati vise poruka
    }

    public function owner()
    {
        return $this->belongsTo(User::class);// grupa pripada jednom vlasniku grupe
    }

}
