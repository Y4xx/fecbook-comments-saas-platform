<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use Illuminate\Http\Request;

class FacebookPageController extends Controller
{
    /**
     * Get all connected Facebook pages for the authenticated user
     */
    public function index(Request $request)
    {
        $pages = $request->user()
            ->facebookPages()
            ->withCount('comments')
            ->latest()
            ->get();

        return inertia('Pages/Index', [
            'pages' => $pages,
        ]);
    }

    /**
     * Get a specific Facebook page
     */
    public function show(FacebookPage $page)
    {
        $this->authorize('view', $page);

        $page->load(['comments' => function ($query) {
            $query->with('sentimentResult')->latest()->limit(50);
        }]);

        return response()->json($page);
    }

    /**
     * Toggle page active status
     */
    public function toggleActive(FacebookPage $page)
    {
        $this->authorize('update', $page);

        $page->update(['is_active' => !$page->is_active]);

        return response()->json([
            'message' => 'Page status updated',
            'page' => $page,
        ]);
    }

    /**
     * Delete a connected page
     */
    public function destroy(FacebookPage $page)
    {
        $this->authorize('delete', $page);

        $page->delete();

        return response()->json([
            'message' => 'Page disconnected successfully',
        ]);
    }
}

