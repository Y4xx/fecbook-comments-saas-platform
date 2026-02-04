<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeCommentSentimentJob;
use App\Models\FacebookComment;
use App\Models\FacebookPage;
use App\Services\FacebookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncFacebookComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-comments {--page-id= : Sync specific page only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Facebook comments from connected pages';

    /**
     * Execute the console command.
     */
    public function handle(FacebookService $facebookService)
    {
        $this->info('Starting Facebook comments sync...');

        // Get pages to sync
        $query = FacebookPage::where('is_active', true);
        
        if ($this->option('page-id')) {
            $query->where('page_id', $this->option('page-id'));
        }
        
        $pages = $query->get();

        if ($pages->isEmpty()) {
            $this->warn('No active Facebook pages found.');
            return 0;
        }

        $totalCommentsImported = 0;

        foreach ($pages as $page) {
            $this->info("Syncing page: {$page->page_name}");
            
            try {
                // Get posts from the page
                $posts = $facebookService->getPagePosts(
                    $page->page_id,
                    $page->access_token,
                    10 // Last 10 posts
                );

                $this->info("Found " . count($posts) . " posts");

                foreach ($posts as $post) {
                    // Get comments for each post
                    $comments = $facebookService->getPostComments(
                        $post['id'],
                        $page->access_token
                    );

                    foreach ($comments as $commentData) {
                        // Store comment if not exists
                        $comment = FacebookComment::firstOrCreate(
                            ['facebook_comment_id' => $commentData['id']],
                            [
                                'facebook_page_id' => $page->id,
                                'post_id' => $post['id'],
                                'message' => $commentData['message'] ?? '',
                                'author_name' => $commentData['from']['name'] ?? 'Unknown',
                                'author_id' => $commentData['from']['id'] ?? '',
                                'comment_created_time' => $commentData['created_time'],
                                'sentiment_status' => 'pending',
                            ]
                        );

                        if ($comment->wasRecentlyCreated) {
                            $totalCommentsImported++;
                            
                            // Dispatch sentiment analysis job
                            AnalyzeCommentSentimentJob::dispatch($comment);
                        }
                    }
                }

                // Update last synced time
                $page->update(['last_synced_at' => now()]);
                
                $this->info("✓ Synced {$page->page_name}");

            } catch (\Exception $e) {
                $this->error("Failed to sync {$page->page_name}: {$e->getMessage()}");
                Log::error('Facebook sync failed', [
                    'page_id' => $page->page_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("✅ Sync completed! Imported {$totalCommentsImported} new comments.");
        
        return 0;
    }
}

