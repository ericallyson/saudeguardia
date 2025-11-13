<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_payload_is_logged_to_whatsapp_webhook_log_file(): void
    {
        $logPath = storage_path('logs/whatsapp-webhooks.log');

        if (file_exists($logPath)) {
            unlink($logPath);
        }

        $payload = [
            'instance_uuid' => 'instance-789',
            'data' => [
                'status' => 'connected',
            ],
            'extra' => 'value',
        ];

        $this->postJson(route('webhooks.whatsapp'), $payload)
            ->assertOk()
            ->assertJson(['received' => true]);

        $this->assertFileExists($logPath);

        $contents = file_get_contents($logPath);

        $this->assertIsString($contents);
        $this->assertTrue(Str::contains($contents, 'Webhook do WhatsApp recebido'));
        $this->assertTrue(Str::contains($contents, 'instance-789'));
        $this->assertTrue(Str::contains($contents, '"extra":"value"'));
    }
}
