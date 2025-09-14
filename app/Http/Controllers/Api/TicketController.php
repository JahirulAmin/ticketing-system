<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    { 
        $user = Auth::user();
        if ($user->role === 'admin') {
            $tickets = Ticket::with(['user', 'comments'])->get();
        } else {
            $tickets = $user->tickets()->with('comments')->get();  // Add relation in User model: public function tickets() { return $this->hasMany(Ticket::class); }
        }
        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,general',
            'priority' => 'required|in:low,medium,high',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048',  // 2MB
        ]);

        $ticket = Auth::user()->tickets()->create($request->only(['subject', 'description', 'category', 'priority']));

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $ticket->attachments()->create(['file_path' => $path]);
        }

        return response()->json($ticket->load('attachments'), 201);
    }

    public function show(Ticket $ticket)
    {
        // Authorization: Customers see only own, admins all
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        return response()->json($ticket->load(['user', 'comments', 'attachments']));
    }

    public function update(Request $request, Ticket $ticket)
    {
        // Same auth check as show
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',  // Only admins update status, but allow customer updates to other fields
        ]);

        if (Auth::user()->role !== 'admin') {
            unset($request['status']);  // Customers can't update status
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

    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Only admins
        if (Auth::user()->role !== 'admin') abort(403);
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->update(['status' => $request->status]);
        return response()->json($ticket);
    }
}
