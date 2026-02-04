<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeCommentSentimentJob;
use App\Models\FacebookComment;
use App\Models\FacebookPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Verify Facebook webhook
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.facebook.webhook_verify_token', 'your_verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle Facebook webhook events
     */
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('Facebook webhook received', ['data' => $data]);

        if (!isset($data['entry'])) {
            return response()->json(['status' => 'ignored']);
        }

        foreach ($data['entry'] as $entry) {
            if (!isset($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                if ($change['field'] !== 'feed') {
                    continue;
                }

                $value = $change['value'];

                // Handle new comment
                if ($value['item'] === 'comment') {
                    $this->handleNewComment($value);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle new comment event
     */
    private function handleNewComment(array $data)
    {
        try {
            // Find the page this comment belongs to
            $page = FacebookPage::where('page_id', $data['from']['id'] ?? '')->first();

            if (!$page) {
                Log::warning('Webhook: Page not found', ['page_id' => $data['from']['id'] ?? '']);
                return;
            }

            // Create or update comment
            $comment = FacebookComment::firstOrCreate(
                ['facebook_comment_id' => $data['comment_id']],
                [
                    'facebook_page_id' => $page->id,
                    'post_id' => $data['post_id'] ?? '',
                    'message' => $data['message'] ?? '',
                    'author_name' => $data['from']['name'] ?? 'Unknown',
                    'author_id' => $data['from']['id'] ?? '',
                    'comment_created_time' => $data['created_time'] ?? now(),
                    'sentiment_status' => 'pending',
                ]
            );

            // Dispatch sentiment analysis job
            AnalyzeCommentSentimentJob::dispatch($comment);

            Log::info('Webhook: New comment processed', ['comment_id' => $comment->id]);

        } catch (\Exception $e) {
            Log::error('Webhook: Failed to process comment', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }
}

