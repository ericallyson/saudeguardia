<?php

namespace App\Services\Subscriptions;

use App\Exceptions\SubscriptionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SubscriptionClient
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(private readonly array $config = [])
    {
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws SubscriptionException
     */
    public function createCustomer(array $payload): array
    {
        $response = $this->request()->post('/customers', $payload);

        if ($response->failed()) {
            throw SubscriptionException::failed('create customer', $response);
        }

        return $response->json('data', []);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws SubscriptionException
     */
    public function createSubscription(int|string $customerId, array $payload): array
    {
        $response = $this->request()->post(
            sprintf('/customers/%s/subscriptions', $customerId),
            $payload
        );

        if ($response->failed()) {
            throw SubscriptionException::failed('create subscription', $response);
        }

        return $response->json('data', []);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SubscriptionException
     */
    public function getLicense(int|string $subscriptionId): array
    {
        $response = $this->request()->get(sprintf('/licenses/%s', $subscriptionId));

        if ($response->failed()) {
            throw SubscriptionException::failed('fetch license', $response);
        }

        return $response->json('data', []);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws SubscriptionException
     */
    public function cancelSubscription(int|string $customerId, int|string $subscriptionId, array $payload = []): array
    {
        $response = $this->request()->post(
            sprintf('/customers/%s/subscriptions/%s/cancel', $customerId, $subscriptionId),
            $payload
        );

        if ($response->failed()) {
            throw SubscriptionException::failed('cancel subscription', $response);
        }

        return $response->json('data', []);
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->withHeaders([
                'Accept' => 'application/json',
            ]);
    }

    protected function baseUrl(): string
    {
        $base = Arr::get($this->config, 'base_url', 'https://assinaturas.saudegaurdia.com.br/api');

        return rtrim($base, '/');
    }
}
