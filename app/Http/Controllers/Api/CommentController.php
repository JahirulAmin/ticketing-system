<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Ticket $ticket)
    {
        // Auth check for ticket access (reuse from TicketController logic)
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        return response()->json($ticket->comments()->with('user')->latest()->get());
    }

    public function store(Request $request, Ticket $ticket)
    {
        // Auth check
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['comment' => 'required|string']);

        $comment = $ticket->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    // Update and delete similar, with auth checks
    public function update(Request $request, Comment $comment)
    {
        if ($comment->ticket->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        $request->validate(['comment' => 'required|string']);
        $comment->update(['comment' => $request->comment]);
        return response()->json($comment);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        $comment->delete();
        return response()->json(['message' => 'Deleted']);
    }
}