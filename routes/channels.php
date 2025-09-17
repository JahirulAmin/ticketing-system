<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{id}', function ($user, $id) {
    $ticket = Ticket::find($id);
    if (!$ticket) {
        return false;
    }
    $isAuthorized = $user->role === 'admin' || $ticket->user_id === $user->id;
    return $isAuthorized;
});