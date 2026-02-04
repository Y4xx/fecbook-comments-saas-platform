<?php

namespace App\Jobs;

use App\Models\FacebookComment;
use App\Services\OpenAISentimentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeCommentSentimentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public FacebookComment $comment
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAISentimentService $sentimentService): void
    {
        try {
            // Mark as analyzing
            $this->comment->update(['sentiment_status' => 'analyzing']);

            // Analyze sentiment
            $result = $sentimentService->analyzeSentiment($this->comment->message);

            if ($result) {
                // Store sentiment result
                $this->comment->sentimentResult()->updateOrCreate(
                    ['facebook_comment_id' => $this->comment->id],
                    [
                        'sentiment' => $result['sentiment'],
                        'confidence' => $result['confidence'],
                        'reason' => $result['reason'],
                    ]
                );

                // Mark as analyzed
                $this->comment->update(['sentiment_status' => 'analyzed']);
            } else {
                // Mark as failed
                $this->comment->update(['sentiment_status' => 'failed']);
            }

        } catch (\Exception $e) {
            Log::error('Sentiment analysis job failed', [
                'comment_id' => $this->comment->id,
                'error' => $e->getMessage(),
            ]);
            
            $this->comment->update(['sentiment_status' => 'failed']);
            throw $e;
        }
    }
}

