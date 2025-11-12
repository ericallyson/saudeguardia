<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Services\Subscriptions\SubscriptionClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SubscriptionClient::class, function ($app): SubscriptionClient {
            return new SubscriptionClient((array) config('services.subscriptions'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
