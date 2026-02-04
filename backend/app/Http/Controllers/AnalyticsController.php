<?php

namespace App\Http\Controllers;

use App\Models\FacebookComment;
use App\Models\SentimentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get analytics dashboard data
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Total comments analyzed
        $totalComments = FacebookComment::whereHas('facebookPage', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        // Sentiment distribution
        $sentimentDistribution = SentimentResult::whereHas('facebookComment.facebookPage', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->select('sentiment', DB::raw('count(*) as count'))
            ->groupBy('sentiment')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->sentiment => $item->count];
            });

        // Calculate percentages
        $analyzedCount = $sentimentDistribution->sum();
        $sentimentPercentages = $sentimentDistribution->map(function ($count) use ($analyzedCount) {
            return $analyzedCount > 0 ? round(($count / $analyzedCount) * 100, 1) : 0;
        });

        // Latest negative comments
        $negativeComments = FacebookComment::whereHas('facebookPage', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereHas('sentimentResult', function ($q) {
                $q->where('sentiment', 'negative');
            })
            ->with(['sentimentResult', 'facebookPage'])
            ->latest('comment_created_time')
            ->limit(10)
            ->get();

        // Comments by status
        $commentsByStatus = FacebookComment::whereHas('facebookPage', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->select('sentiment_status', DB::raw('count(*) as count'))
            ->groupBy('sentiment_status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->sentiment_status => $item->count];
            });

        return inertia('Analytics/Index', [
            'stats' => [
                'totalComments' => $totalComments,
                'analyzedComments' => $analyzedCount,
                'pendingComments' => $commentsByStatus['pending'] ?? 0,
                'sentimentDistribution' => $sentimentDistribution,
                'sentimentPercentages' => $sentimentPercentages,
            ],
            'negativeComments' => $negativeComments,
        ]);
    }
}

