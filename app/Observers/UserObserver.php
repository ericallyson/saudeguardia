<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Subscriptions\SubscriptionManager;

class UserObserver
{
    public function __construct(private readonly SubscriptionManager $subscriptions)
    {
    }

    public function created(User $user): void
    {
        $this->subscriptions->provisionForUser($user);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['name', 'email'])) {
            $this->subscriptions->refreshSubscription($user);
        }
    }
}
