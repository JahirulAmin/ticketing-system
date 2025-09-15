<?php
namespace App\Events;
use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class TicketChatSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->chat->ticket_id);
    }

    public function broadcastWith()
    {
        return ['chat' => $this->chat->load('user')];
    }
}