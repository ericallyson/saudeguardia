<?php

namespace Tests\Unit\Subscriptions;

use App\Services\Subscriptions\SubscriptionClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionClientTest extends TestCase
{
    public function test_list_plans_uses_normalized_base_url(): void
    {
        Http::fake([
            'https://assinaturas.saudeguardia.com.br/api/plans' => Http::response([
                'data' => [
                    ['id' => 30, 'name' => 'Plano de testes'],
                ],
            ]),
        ]);

        $client = new SubscriptionClient([
            'base_url' => 'https://assinaturas.saudegaurdia.com.br/api',
        ]);

        $plans = $client->listPlans();

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://assinaturas.saudeguardia.com.br/api/plans';
        });

        $this->assertSame([
            ['id' => 30, 'name' => 'Plano de testes'],
        ], $plans);
    }
}
