<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Facebook Socialite provider
        $socialite = $this->app->make(SocialiteFactory::class);
        
        $socialite->extend('facebook', function ($app) use ($socialite) {
            $config = $app['config']['services.facebook'];
            return $socialite->buildProvider(
                \SocialiteProviders\Facebook\Provider::class,
                $config
            );
        });
    }
}
