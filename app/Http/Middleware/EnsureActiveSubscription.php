<?php

namespace App\Http\Middleware;

use App\Services\Subscriptions\SubscriptionManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function __construct(private readonly SubscriptionManager $subscriptions)
    {
    }

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($this->subscriptions->hasValidSubscription($user)) {
            return $next($request);
        }

        $context = $this->subscriptions->lastRequestContext();

        if ($context !== []) {
            Log::warning('Inactive subscription detected during authentication', $context + [
                'user_id' => $user->id,
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors([
                'email' => __('Sua assinatura não está ativa. Entre em contato com o suporte.'),
            ])
            ->with('subscription_debug', $context);
    }
}
