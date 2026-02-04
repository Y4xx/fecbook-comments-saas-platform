<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI;

class OpenAISentimentService
{
    private $client;

    public function __construct()
    {
        $apiKey = config('services.openai.api_key');
        
        if ($apiKey) {
            $this->client = OpenAI::client($apiKey);
        }
    }

    /**
     * Analyze sentiment of a comment
     *
     * @param string $commentText
     * @return array{sentiment: string, confidence: float, reason: string}|null
     */
    public function analyzeSentiment(string $commentText): ?array
    {
        if (!$this->client) {
            Log::warning('OpenAI client not initialized - API key missing');
            return null;
        }

        try {
            $prompt = $this->buildPrompt($commentText);
            
            $response = $this->client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a sentiment analysis expert. Analyze the sentiment of social media comments and respond ONLY with valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 200,
            ]);

            $content = $response->choices[0]->message->content;
            
            // Parse JSON response
            $result = json_decode($content, true);
            
            if (!$result || !isset($result['sentiment'])) {
                Log::error('Invalid OpenAI response format', ['response' => $content]);
                return null;
            }

            // Validate sentiment value
            $validSentiments = ['positive', 'negative', 'neutral'];
            if (!in_array(strtolower($result['sentiment']), $validSentiments)) {
                $result['sentiment'] = 'neutral';
            }

            return [
                'sentiment' => strtolower($result['sentiment']),
                'confidence' => floatval($result['confidence'] ?? 0.5),
                'reason' => $result['reason'] ?? 'No reason provided',
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI sentiment analysis failed', [
                'error' => $e->getMessage(),
                'comment' => substr($commentText, 0, 100),
            ]);
            return null;
        }
    }

    /**
     * Build the prompt for sentiment analysis
     */
    private function buildPrompt(string $commentText): string
    {
        return <<<PROMPT
Analyze the sentiment of this Facebook comment and classify it as positive, negative, or neutral.

Comment: "{$commentText}"

Respond with ONLY this JSON format (no other text):
{
  "sentiment": "positive|negative|neutral",
  "confidence": 0.0-1.0,
  "reason": "Brief explanation"
}
PROMPT;
    }

    /**
     * Analyze multiple comments in batch
     */
    public function analyzeBatch(array $comments): array
    {
        $results = [];
        
        foreach ($comments as $comment) {
            $result = $this->analyzeSentiment($comment);
            if ($result) {
                $results[] = $result;
            }
        }
        
        return $results;
    }
}
