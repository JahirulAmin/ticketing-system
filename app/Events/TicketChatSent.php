<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketChatSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
        Log::info('TicketChatSent event constructed for chat ID: ' . $this->chat->id);
    }

    public function broadcastOn()
    {
        Log::info('broadcastOn called for chat ID: ' . $this->chat->id . ', channel: ticket.' . $this->chat->ticket_id);
        return new Channel('ticket.' . $this->chat->ticket_id);
    }

    public function broadcastWith()
    {
        Log::info('broadcastWith called for chat ID: ' . $this->chat->id);
        return ['chat' => $this->chat->load('user')];
    }
}