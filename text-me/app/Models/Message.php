<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\MessageObserver;

#[ObservedBy([MessageObserver::class])]
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'sender_id',
        'receiver_id',
        'group_id',
        'gif_url'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');//poruka je poslata od strane jednog korisnika
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');//poslata poruka pripada jednom primaocu
    }

    public function group()
    {
        return $this->belongsTo(Group::class);//poruka je poslata jednoj i samo jednoj grupi
    }


    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);// jedna poruka moze imati vise atachmenta
    }
       

}
