<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Chat extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'message'];

    protected static function booted()
    {
        static::created(function ($chat) {
            \Illuminate\Support\Facades\Log::info('Chat created event triggered for chat ID: ' . $chat->id);
            \Illuminate\Support\Facades\Log::info('Attempting to broadcast TicketChatSent for chat ID: ' . $chat->id);
            $event = new \App\Events\TicketChatSent($chat);
            broadcast($event)->toOthers();
            \Illuminate\Support\Facades\Log::info('Broadcast attempted for chat ID: ' . $chat->id);
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
