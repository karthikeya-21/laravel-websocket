<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Chats extends Model
{
    use HasFactory;

    protected $fillable=[
        'from_user_id',
        'to_user_id',
        'chat_message',
        'message_status'
    ];

    public function setChatMessageAttribute($value)
    {
        $this->attributes['chat_message'] = Crypt::encryptString($value);
    }

    public function getChatMessageAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}
