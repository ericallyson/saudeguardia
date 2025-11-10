<?php

namespace App\Services\Subscriptions;

use App\Exceptions\SubscriptionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SubscriptionClient
{
    /**
     * @var array<string, mixed>
     */
    protected array $lastRequestContext = [];

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
        $path = '/customers';
        $response = $this->request()->post($path, $payload);
        $this->rememberRequest('POST', $path, $payload, 'create customer', $response);

        if ($response->failed()) {
            throw SubscriptionException::failed('create customer', $response, $this->lastRequestContext);
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
        $path = sprintf('/customers/%s/subscriptions', $customerId);
        $response = $this->request()->post($path, $payload);
        $this->rememberRequest('POST', $path, $payload, 'create subscription', $response);

        if ($response->failed()) {
            throw SubscriptionException::failed('create subscription', $response, $this->lastRequestContext);
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
        $path = sprintf('/licenses/%s', $subscriptionId);
        $response = $this->request()->get($path);
        $this->rememberRequest('GET', $path, [], 'fetch license', $response);

        if ($response->failed()) {
            throw SubscriptionException::failed('fetch license', $response, $this->lastRequestContext);
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
        $path = sprintf('/customers/%s/subscriptions/%s/cancel', $customerId, $subscriptionId);
        $response = $this->request()->post($path, $payload);
        $this->rememberRequest('POST', $path, $payload, 'cancel subscription', $response);

        if ($response->failed()) {
            throw SubscriptionException::failed('cancel subscription', $response, $this->lastRequestContext);
        }

        return $response->json('data', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function lastRequestContext(): array
    {
        return $this->lastRequestContext;
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
        $base = Arr::get($this->config, 'base_url', 'https://assinaturas.saudeguardia.com.br/api');

        return rtrim($base, '/');
    }

    protected function makeUrl(string $path): string
    {
        return $this->baseUrl().'/'.ltrim($path, '/');
    }

    protected function rememberRequest(string $method, string $path, array $parameters, string $operation, Response $response): void
    {
        $this->lastRequestContext = [
            'operation' => $operation,
            'method' => $method,
            'url' => $this->makeUrl($path),
            'parameters' => $parameters,
            'status' => $response->status(),
            'response' => $response->json(),
        ];
    }
}
