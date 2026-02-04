<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    private Client $client;
    private string $apiVersion = 'v21.0';
    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}";
    }

    /**
     * Get user's Facebook pages
     */
    public function getUserPages(string $accessToken): array
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/me/accounts", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,name,access_token,category',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch Facebook pages', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get page posts
     */
    public function getPagePosts(string $pageId, string $accessToken, int $limit = 25): array
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/{$pageId}/posts", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,message,created_time',
                    'limit' => $limit,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch page posts', [
                'page_id' => $pageId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get comments for a post
     */
    public function getPostComments(string $postId, string $accessToken): array
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/{$postId}/comments", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,message,from,created_time',
                    'limit' => 100,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch post comments', [
                'post_id' => $postId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Exchange short-lived token for long-lived token
     */
    public function getLongLivedToken(string $shortLivedToken): ?string
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/oauth/access_token", [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => config('services.facebook.client_id'),
                    'client_secret' => config('services.facebook.client_secret'),
                    'fb_exchange_token' => $shortLivedToken,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to exchange token', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
