<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FacebookPage;
use App\Models\FacebookComment;
use App\Models\SentimentResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo user
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create a demo Facebook page
        $page = FacebookPage::create([
            'user_id' => $user->id,
            'page_id' => '123456789',
            'page_name' => 'Demo Business Page',
            'access_token' => 'demo_token_' . bin2hex(random_bytes(32)),
            'is_active' => true,
            'last_synced_at' => now()->subHours(2),
        ]);

        // Sample comments with different sentiments
        $sampleComments = [
            [
                'message' => 'Great product! I absolutely love it. The quality is amazing and the customer service is excellent.',
                'author_name' => 'John Smith',
                'sentiment' => 'positive',
                'confidence' => 0.95,
                'reason' => 'Customer expresses strong satisfaction with product quality and service',
            ],
            [
                'message' => 'Terrible experience. The product broke after 2 days and customer support never responded.',
                'author_name' => 'Sarah Johnson',
                'sentiment' => 'negative',
                'confidence' => 0.92,
                'reason' => 'Customer reports product failure and poor customer support',
            ],
            [
                'message' => 'The product is okay, nothing special. Does what it says but nothing more.',
                'author_name' => 'Mike Wilson',
                'sentiment' => 'neutral',
                'confidence' => 0.78,
                'reason' => 'Customer feedback is balanced without strong positive or negative emotions',
            ],
            [
                'message' => 'Best purchase I made this year! Highly recommend to everyone!',
                'author_name' => 'Emily Davis',
                'sentiment' => 'positive',
                'confidence' => 0.98,
                'reason' => 'Strong recommendation and enthusiasm about the purchase',
            ],
            [
                'message' => 'Disappointed with the quality. Expected better for this price point.',
                'author_name' => 'Robert Brown',
                'sentiment' => 'negative',
                'confidence' => 0.85,
                'reason' => 'Customer expresses disappointment with value for money',
            ],
            [
                'message' => 'Amazing! This exceeded all my expectations. Worth every penny!',
                'author_name' => 'Lisa Garcia',
                'sentiment' => 'positive',
                'confidence' => 0.97,
                'reason' => 'Customer is highly satisfied, product exceeded expectations',
            ],
            [
                'message' => 'It\'s fine. Does the job. Nothing to complain about really.',
                'author_name' => 'David Martinez',
                'sentiment' => 'neutral',
                'confidence' => 0.72,
                'reason' => 'Neutral feedback, meets basic expectations',
            ],
            [
                'message' => 'Worst product ever! Complete waste of money. Do not buy!',
                'author_name' => 'Jennifer Lee',
                'sentiment' => 'negative',
                'confidence' => 0.99,
                'reason' => 'Extremely negative review with strong warning to others',
            ],
            [
                'message' => 'Pretty good overall. A few minor issues but generally satisfied.',
                'author_name' => 'James Anderson',
                'sentiment' => 'positive',
                'confidence' => 0.82,
                'reason' => 'Mostly positive with minor reservations',
            ],
            [
                'message' => 'Not sure how I feel about this. Has pros and cons.',
                'author_name' => 'Maria Rodriguez',
                'sentiment' => 'neutral',
                'confidence' => 0.65,
                'reason' => 'Mixed feelings, balanced perspective',
            ],
        ];

        foreach ($sampleComments as $index => $commentData) {
            $comment = FacebookComment::create([
                'facebook_page_id' => $page->id,
                'facebook_comment_id' => 'comment_' . ($index + 1) . '_' . time(),
                'post_id' => 'post_123_' . time(),
                'message' => $commentData['message'],
                'author_name' => $commentData['author_name'],
                'author_id' => 'user_' . ($index + 1),
                'comment_created_time' => now()->subDays(rand(1, 30)),
                'sentiment_status' => 'analyzed',
            ]);

            SentimentResult::create([
                'facebook_comment_id' => $comment->id,
                'sentiment' => $commentData['sentiment'],
                'confidence' => $commentData['confidence'],
                'reason' => $commentData['reason'],
            ]);
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('📧 Email: demo@example.com');
        $this->command->info('🔑 Password: password');
    }
}
