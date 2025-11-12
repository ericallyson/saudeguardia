<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_updates_user_status_and_qr_code(): void
    {
        $user = User::factory()->create([
            'whatsapp_instance_uuid' => 'instance-123',
            'whatsapp_instance_status' => null,
            'whatsapp_qr_code_base64' => null,
        ]);

        $payload = [
            'instance_uuid' => 'instance-123',
            'data' => [
                'status' => 'connected',
                'qr_code_base64' => base64_encode('fake-qr-code'),
            ],
        ];

        $response = $this->postJson(route('webhooks.whatsapp'), $payload);

        $response->assertOk()->assertJson(['received' => true]);

        $user->refresh();

        $this->assertSame('connected', $user->whatsapp_instance_status);
        $this->assertSame($payload['data']['qr_code_base64'], $user->whatsapp_qr_code_base64);
    }

    public function test_webhook_clears_qr_code_when_status_is_not_qr_code(): void
    {
        $user = User::factory()->create([
            'whatsapp_instance_uuid' => 'instance-456',
            'whatsapp_instance_status' => 'qr_code',
            'whatsapp_qr_code_base64' => base64_encode('old-qr'),
        ]);

        $payload = [
            'instance_uuid' => 'instance-456',
            'data' => [
                'status' => 'connected',
            ],
        ];

        $response = $this->postJson(route('webhooks.whatsapp'), $payload);

        $response->assertOk()->assertJson(['received' => true]);

        $user->refresh();

        $this->assertSame('connected', $user->whatsapp_instance_status);
        $this->assertNull($user->whatsapp_qr_code_base64);
    }
}
