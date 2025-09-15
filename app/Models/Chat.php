<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'message'];

    protected static function booted()
    {
        static::created(function ($chat) {
            broadcast(new \App\Events\TicketChatSent($chat));
        });
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
