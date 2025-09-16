<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        return response()->json($ticket->chats()->with('user')->get());
    }

    public function store(Request $request, Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['message' => 'required|string|max:1000']);

        $chat = $ticket->chats()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json($chat->load('user'), 201);
    }
}