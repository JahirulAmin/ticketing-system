<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('ticket.{id}', function ($user, $id) {
//     Log::info('Authorizing user ID: ' . $user->id . ' for ticket ID: ' . $id);
//     return true; // Restrictive
// });

Broadcast::channel('ticket.{id}', function ($user, $id) {
    Log::info('Authorizing user ID: ' . $user->id . ' for ticket ID: ' . $id);
    $ticket = Ticket::find($id);
    if (!$ticket) {
        Log::warning('Ticket not found: ' . $id);
        return false;
    }
    $isAuthorized = $user->role === 'admin' || $ticket->user_id === $user->id;
    Log::info('Authorization result for user ID ' . $user->id . ' on ticket ID ' . $id . ': ' . ($isAuthorized ? 'granted' : 'denied'));
    return $isAuthorized;
});