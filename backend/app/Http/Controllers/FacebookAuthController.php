<?php

namespace App\Http\Controllers;

use App\Models\FacebookPage;
use App\Services\FacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class FacebookAuthController extends Controller
{
    public function __construct(
        private FacebookService $facebookService
    ) {
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes([
                'pages_read_engagement',
                'pages_manage_metadata',
                'pages_show_list',
                'read_insights',
            ])
            ->redirect();
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function callback(Request $request)
    {
        try {
            $socialiteUser = Socialite::driver('facebook')->user();
            
            // Get long-lived token
            $longLivedToken = $this->facebookService->getLongLivedToken($socialiteUser->token);
            
            if (!$longLivedToken) {
                return redirect('/dashboard?error=token_exchange_failed');
            }

            // Get user's pages
            $pages = $this->facebookService->getUserPages($longLivedToken);

            // Store pages in session for selection
            session(['facebook_pages' => $pages, 'facebook_token' => $longLivedToken]);

            return redirect('/facebook/select-page');

        } catch (\Exception $e) {
            Log::error('Facebook OAuth failed', ['error' => $e->getMessage()]);
            return redirect('/dashboard?error=auth_failed');
        }
    }

    /**
     * Show page selection
     */
    public function selectPage()
    {
        $pages = session('facebook_pages', []);
        
        if (empty($pages)) {
            return redirect('/dashboard?error=no_pages');
        }

        return inertia('Facebook/SelectPage', [
            'pages' => $pages,
        ]);
    }

    /**
     * Connect selected page
     */
    public function connectPage(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
            'page_name' => 'required|string',
            'access_token' => 'required|string',
        ]);

        try {
            $page = FacebookPage::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'page_id' => $request->page_id,
                ],
                [
                    'page_name' => $request->page_name,
                    'access_token' => $request->access_token,
                    'is_active' => true,
                ]
            );

            // Clear session data
            session()->forget(['facebook_pages', 'facebook_token']);

            return redirect('/dashboard')->with('success', 'Facebook page connected successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to connect page', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to connect page']);
        }
    }
}

