<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HomeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.subscriptions.base_url' => 'https://assinaturas.test',
        ]);
    }

    public function test_it_registers_user_and_redirects_to_payment_page(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-03-01 12:00:00'));

        $plan = [
            'id' => 1,
            'name' => 'Plano Pro',
            'slug' => 'plano-pro',
            'billing_period' => 'monthly',
            'trial_days' => 7,
            'prices' => [
                'monthly' => 129.9,
            ],
        ];

        $customer = [
            'id' => 5,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ];

        $subscription = [
            'id' => 10,
            'customer_id' => $customer['id'],
            'status' => 'active',
            'starts_at' => '2024-03-01',
            'trial_ends_at' => '2024-03-08',
            'next_renewal_date' => '2024-04-08',
            'price' => 129.9,
            'plan' => [
                'id' => $plan['id'],
                'name' => $plan['name'],
                'slug' => $plan['slug'],
            ],
            'payment_url' => 'https://payments.test/checkout',
        ];

        Http::fake([
            'https://assinaturas.test/api/plans' => Http::response(['data' => [$plan]], 200),
            'https://assinaturas.test/api/customers' => Http::response(['data' => $customer], 201),
            'https://assinaturas.test/api/customers/5/subscriptions' => Http::response(['data' => $subscription], 201),
        ]);

        $response = $this->post(route('home.register'), [
            'plan_id' => $plan['id'],
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+55 11 99999-0000',
            'document_type' => 'CPF',
            'document_number' => '12345678901',
        ]);

        $response->assertRedirect('https://payments.test/checkout');

        $user = User::where('email', 'john@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));

        $this->assertSame($customer['id'], $user->subscription_customer_id);
        $this->assertSame($subscription['id'], $user->subscription_id);
        $this->assertSame($plan['id'], $user->subscription_plan_id);
        $this->assertSame($plan['name'], $user->subscription_plan_name);
        $this->assertSame($plan['slug'], $user->subscription_plan_slug);
        $this->assertSame($subscription['status'], $user->subscription_status);
        $this->assertEquals('2024-03-08', optional($user->subscription_trial_ends_at)->toDateString());
        $this->assertEquals('2024-04-08', optional($user->subscription_next_renewal_date)->toDateString());
        $this->assertSame('129.90', (string) $user->subscription_price);
        $this->assertSame('landing-page', data_get($user->subscription_metadata, 'subscription.source'));

        Carbon::setTestNow();
    }

    public function test_it_preselects_plan_and_opens_modal_from_query_parameters(): void
    {
        $plan = [
            'id' => 3,
            'name' => 'Plano Essencial',
            'slug' => 'plano-essencial',
            'billing_period' => 'monthly',
            'trial_days' => 14,
            'prices' => [
                'monthly' => 89.9,
            ],
        ];

        Http::fake([
            'https://assinaturas.test/api/plans' => Http::response(['data' => [$plan]], 200),
        ]);

        $response = $this->get(route('home', ['plan' => $plan['id'], 'register' => 1]));

        $response->assertOk();
        $response->assertViewIs('home.index');
        $response->assertViewHas('selectedPlanId', $plan['id']);
        $response->assertViewHas('shouldOpenRegistration', true);
        $response->assertSee('data-should-open="true"', false);
        $response->assertSee('id="selected-plan-id" value="3"', false);
        $response->assertSee('Plano Essencial', false);
    }

    public function test_it_returns_with_error_when_subscription_api_fails(): void
    {
        $plan = [
            'id' => 1,
            'name' => 'Plano Pro',
            'slug' => 'plano-pro',
            'billing_period' => 'monthly',
            'prices' => [
                'monthly' => 129.9,
            ],
        ];

        Http::fake([
            'https://assinaturas.test/api/plans' => Http::response(['data' => [$plan]], 200),
            'https://assinaturas.test/api/customers' => Http::response(['message' => 'Error'], 500),
        ]);

        $response = $this
            ->from(route('home'))
            ->post(route('home.register'), [
                'plan_id' => $plan['id'],
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('registration');
        $response->assertSessionHasInput('name', 'John Doe');
        $response->assertSessionHasInput('email', 'john@example.com');
        $response->assertSessionHasInput('plan_id', $plan['id']);

        $this->assertNull(session()->getOldInput('password'));

        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }
}
