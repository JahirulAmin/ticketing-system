<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TicketRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    { 
        $user = Auth::user();
        if ($user->role === 'admin') {
            $tickets = Ticket::with(['user', 'comments'])->latest()->get();
        } else {
            $tickets = $user->tickets()->with('comments')->latest()->get();  // Add relation in User model: public function tickets() { return $this->hasMany(Ticket::class); }
        }
        return response()->json($tickets);
    }

    public function store(TicketRequest $request)
    {
        $ticket = Auth::user()->tickets()->create($request->only(['subject', 'description', 'category', 'priority']));
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $ticket->attachments()->create(['file_path' => $path]);
        }
        return response()->json($ticket->load('attachments'), 201);
    }

    public function show(Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        return response()->json($ticket->load(['user', 'comments', 'attachments']));
    }

    public function update(TicketRequest $request, Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if (Auth::user()->role !== 'admin') {
            unset($request['status']);  
        }

        $ticket->update($request->only(['subject', 'description', 'status']));

        return response()->json($ticket);
    }

    public function destroy(Ticket $ticket)
    {
        // Same auth
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        $ticket->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function status(Ticket $ticket)
    {
        // Auth check as above
        return response()->json(['status' => $ticket->status]);
    }

    public function updateStatus(TicketRequest $request, Ticket $ticket)
    {
        // Only admins
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->update(['status' => $request->status]);
        return response()->json($ticket);
    }
}
