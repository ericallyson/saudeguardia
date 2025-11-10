<?php

namespace Tests\Unit\Subscriptions;

use App\Models\User;
use App\Services\Subscriptions\SubscriptionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionManagerTest extends TestCase
{
    use RefreshDatabase;

    public function testProvisionForUserCreatesCustomerSubscriptionAndRefreshes(): void
    {
        config()->set('services.subscriptions.default_plan_id', 9);
        config()->set('services.subscriptions.default_status', 'active');
        config()->set('services.subscriptions.default_price', 99.9);
        config()->set('services.subscriptions.base_url', 'https://assinaturas.saudeguardia.com.br/api');

        Http::fake([
            'https://assinaturas.saudeguardia.com.br/api/customers' => Http::response([
                'data' => [
                    'id' => 123,
                    'first_name' => 'Ana',
                    'last_name' => 'Silva',
                    'email' => 'ana@example.com',
                    'status' => 'active',
                ],
            ], 201),
            'https://assinaturas.saudeguardia.com.br/api/customers/123/subscriptions' => Http::response([
                'data' => [
                    'id' => 456,
                    'customer_id' => 123,
                    'status' => 'active',
                    'starts_at' => '2024-01-01',
                    'next_renewal_date' => '2024-02-01',
                    'price' => 99.9,
                    'plan' => [
                        'id' => 9,
                        'name' => 'Plano Pro',
                        'slug' => 'plano-pro',
                        'features' => [
                            ['id' => 10, 'name' => 'Usuários', 'key' => 'users'],
                        ],
                    ],
                    'metadata' => [
                        'payment_gateway' => 'stripe',
                    ],
                ],
            ], 201),
            'https://assinaturas.saudeguardia.com.br/api/licenses/456' => Http::response([
                'data' => [
                    'id' => 456,
                    'status' => 'active',
                    'trial_ends_at' => null,
                    'next_renewal_date' => '2024-02-01',
                    'price' => 99.9,
                    'metadata' => [
                        'payment_gateway' => 'stripe',
                    ],
                    'customer' => [
                        'id' => 123,
                    ],
                    'plan' => [
                        'id' => 9,
                        'name' => 'Plano Pro',
                        'slug' => 'plano-pro',
                        'features' => [
                            ['id' => 10, 'name' => 'Usuários', 'key' => 'users'],
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create([
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $manager = app(SubscriptionManager::class);

        $this->assertTrue($manager->provisionForUser($user));

        $user->refresh();

        $this->assertSame(123, $user->subscription_customer_id);
        $this->assertSame(456, $user->subscription_id);
        $this->assertSame('active', $user->subscription_status);
        $this->assertSame('Plano Pro', $user->subscription_plan_name);
        $this->assertSame('plano-pro', $user->subscription_plan_slug);
        $this->assertNotNull($user->subscription_last_synced_at);
        $this->assertTrue($user->hasActiveSubscription());

        Http::assertSentCount(3);
    }

    public function testHasValidSubscriptionReturnsFalseWhenStatusDelinquent(): void
    {
        config()->set('services.subscriptions.default_plan_id', 9);

        Http::fake([
            'https://assinaturas.saudeguardia.com.br/api/licenses/456' => Http::response([
                'data' => [
                    'id' => 456,
                    'status' => 'delinquent',
                    'trial_ends_at' => null,
                    'next_renewal_date' => '2024-02-01',
                    'price' => 99.9,
                    'customer' => [
                        'id' => 123,
                    ],
                    'plan' => [
                        'id' => 9,
                        'name' => 'Plano Pro',
                        'slug' => 'plano-pro',
                        'features' => [],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create([
            'subscription_customer_id' => 123,
            'subscription_id' => 456,
            'subscription_status' => 'delinquent',
            'subscription_next_renewal_date' => now()->subDay(),
        ]);

        $manager = app(SubscriptionManager::class);

        $this->assertFalse($manager->hasValidSubscription($user));
        $this->assertSame('delinquent', $user->fresh()->subscription_status);

        Http::assertSentCount(1);
    }

    public function testHasValidSubscriptionSkipsHttpWhenAlreadyValid(): void
    {
        Http::fake();

        $user = User::factory()->create([
            'subscription_customer_id' => 321,
            'subscription_id' => 654,
            'subscription_status' => 'active',
            'subscription_next_renewal_date' => now()->addDays(5),
            'subscription_last_synced_at' => now(),
        ]);

        $manager = app(SubscriptionManager::class);

        $this->assertTrue($manager->hasValidSubscription($user));

        Http::assertSentCount(0);
    }
}
