<?php

namespace App\Http\Controllers;

use App\Models\FacebookComment;
use App\Models\FacebookPage;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Get comments for analysis view
     */
    public function index(Request $request)
    {
        $query = FacebookComment::query()
            ->whereHas('facebookPage', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->with(['sentimentResult', 'facebookPage']);

        // Filter by sentiment if provided
        if ($request->has('sentiment')) {
            $query->whereHas('sentimentResult', function ($q) use ($request) {
                $q->where('sentiment', $request->sentiment);
            });
        }

        // Filter by page if provided
        if ($request->has('page_id')) {
            $query->where('facebook_page_id', $request->page_id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('sentiment_status', $request->status);
        }

        $comments = $query->latest('comment_created_time')
            ->paginate(50);

        return inertia('Comments/Index', [
            'comments' => $comments,
            'filters' => $request->only(['sentiment', 'page_id', 'status']),
        ]);
    }

    /**
     * Get a specific comment
     */
    public function show(FacebookComment $comment)
    {
        // Ensure user owns this comment through the page
        if ($comment->facebookPage->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->load(['sentimentResult', 'facebookPage']);

        return response()->json($comment);
    }
}

