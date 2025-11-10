<?php

namespace App\Services\Subscriptions;

use App\Exceptions\SubscriptionException;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscriptionManager
{
    /**
     * @var array<string, mixed>
     */
    protected array $lastRequestContext = [];

    public function __construct(private readonly SubscriptionClient $client)
    {
    }

    public function hasValidSubscription(User $user): bool
    {
        $this->lastRequestContext = [];

        if ($this->isSubscriptionValid($user)) {
            return true;
        }

        return $this->provisionForUser($user);
    }

    public function provisionForUser(User $user): bool
    {
        try {
            if (! $user->subscription_customer_id) {
                $this->createCustomerForUser($user);
            }

            if (! $user->subscription_id && $user->subscription_customer_id && $this->defaultPlanId()) {
                $this->createSubscriptionForUser($user);
            }

            if ($user->subscription_id) {
                $this->refreshSubscription($user);
            }

        } catch (SubscriptionException $exception) {
            $this->rememberLastRequest();
            $context = array_merge($this->lastRequestContext, $exception->context());
            $this->lastRequestContext = $context;

            Log::warning($exception->getMessage(), $context + ['user_id' => $user->id]);
        } catch (Throwable $exception) {
            Log::error('Unexpected error while provisioning subscription', [
                'user_id' => $user->id,
                'exception' => $exception,
            ]);
        }

        return $this->isSubscriptionValid($user);
    }

    public function refreshSubscription(User $user): ?array
    {
        if (! $user->subscription_id) {
            return null;
        }

        try {
            $data = $this->client->getLicense($user->subscription_id);
            $this->rememberLastRequest();
            $plan = Arr::get($data, 'plan', []);
            $customer = Arr::get($data, 'customer', []);

            $user->forceFill([
                'subscription_customer_id' => Arr::get($customer, 'id', $user->subscription_customer_id),
                'subscription_plan_id' => Arr::get($plan, 'id'),
                'subscription_plan_name' => Arr::get($plan, 'name'),
                'subscription_plan_slug' => Arr::get($plan, 'slug'),
                'subscription_status' => Arr::get($data, 'status'),
                'subscription_trial_ends_at' => Arr::get($data, 'trial_ends_at'),
                'subscription_next_renewal_date' => Arr::get($data, 'next_renewal_date'),
                'subscription_price' => Arr::get($data, 'price'),
                'subscription_metadata' => [
                    'subscription' => Arr::get($data, 'metadata'),
                    'plan_features' => Arr::get($plan, 'features'),
                ],
                'subscription_last_synced_at' => now(),
            ])->save();

            return $data;
        } catch (SubscriptionException $exception) {
            $this->rememberLastRequest();
            $context = array_merge($this->lastRequestContext, $exception->context());
            $this->lastRequestContext = $context;

            Log::warning($exception->getMessage(), $context + ['user_id' => $user->id]);
        }

        return null;
    }

    protected function createCustomerForUser(User $user): ?array
    {
        [$firstName, $lastName] = $this->splitName($user->name);

        $payload = array_filter([
            'first_name' => $firstName ?: $user->email,
            'last_name' => $lastName,
            'email' => $user->email,
            'status' => $this->defaultStatus(),
            'notes' => 'Gerado automaticamente pelo app Saúde Guardiã',
        ], fn ($value) => $value !== null && $value !== '');

        $data = $this->client->createCustomer($payload);
        $this->rememberLastRequest();

        $user->forceFill([
            'subscription_customer_id' => Arr::get($data, 'id'),
            'subscription_status' => Arr::get($data, 'status', $user->subscription_status),
        ])->save();

        return $data;
    }

    protected function createSubscriptionForUser(User $user): ?array
    {
        $customerId = $user->subscription_customer_id;
        $planId = $this->defaultPlanId();

        if (! $customerId || ! $planId) {
            return null;
        }

        $payload = [
            'plan_id' => $planId,
            'status' => $this->defaultStatus(),
            'starts_at' => now()->toDateString(),
            'price' => $this->defaultPrice(),
            'metadata' => [
                'source' => 'saudeguardia-app',
                'user_id' => $user->id,
                'user_email' => $user->email,
            ],
        ];

        if ($payload['price'] === null) {
            unset($payload['price']);
        }

        $data = $this->client->createSubscription($customerId, $payload);
        $this->rememberLastRequest();
        $plan = Arr::get($data, 'plan', []);

        $user->forceFill([
            'subscription_id' => Arr::get($data, 'id'),
            'subscription_plan_id' => Arr::get($plan, 'id'),
            'subscription_plan_name' => Arr::get($plan, 'name'),
            'subscription_plan_slug' => Arr::get($plan, 'slug'),
            'subscription_status' => Arr::get($data, 'status'),
            'subscription_trial_ends_at' => Arr::get($data, 'trial_ends_at'),
            'subscription_next_renewal_date' => Arr::get($data, 'next_renewal_date'),
            'subscription_price' => Arr::get($data, 'price'),
            'subscription_metadata' => [
                'subscription' => Arr::get($data, 'metadata'),
                'plan_features' => Arr::get($plan, 'features'),
            ],
            'subscription_last_synced_at' => now(),
        ])->save();

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function lastRequestContext(): array
    {
        return $this->lastRequestContext;
    }

    protected function defaultPlanId(): ?int
    {
        $plan = config('services.subscriptions.default_plan_id');

        return $plan !== null && $plan !== '' ? (int) $plan : null;
    }

    protected function defaultStatus(): string
    {
        return (string) (config('services.subscriptions.default_status') ?: 'active');
    }

    protected function defaultPrice(): ?float
    {
        $price = config('services.subscriptions.default_price');

        return $price !== null && $price !== '' ? (float) $price : null;
    }

    protected function isSubscriptionValid(User $user): bool
    {
        return $user->hasActiveSubscription();
    }

    protected function rememberLastRequest(): void
    {
        $context = $this->client->lastRequestContext();

        if ($context !== []) {
            $this->lastRequestContext = $context;
        }
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    protected function splitName(?string $name): array
    {
        $name = trim((string) $name);

        if ($name === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $first = array_shift($parts);
        $last = $parts ? implode(' ', $parts) : null;

        return [$first, $last];
    }
}
