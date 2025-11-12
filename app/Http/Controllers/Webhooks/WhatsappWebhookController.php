<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('Webhook do WhatsApp recebido', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        if ($request->input('event') === 'connection.update') {
            $instanceUuid = $request->input('instance_uuid');
            $status = Arr::get($request->input('data', []), 'status');
            $qrCode = $this->extractQrCode($request);

            if (! $instanceUuid) {
                Log::warning('Webhook de conexão recebido sem UUID da instância.');
            } elseif (! is_string($status) || $status === '') {
                Log::warning('Webhook de conexão recebido sem status válido.', [
                    'instance_uuid' => $instanceUuid,
                ]);
            } else {
                $user = User::where('whatsapp_instance_uuid', $instanceUuid)->first();

                if (! $user) {
                    Log::warning('Webhook de conexão recebido para instância desconhecida.', [
                        'instance_uuid' => $instanceUuid,
                    ]);
                } else {
                    $updates = [
                        'whatsapp_instance_status' => $status,
                    ];

                    if ($qrCode !== null) {
                        $updates['whatsapp_qr_code_base64'] = $qrCode;
                    } elseif ($status && $status !== 'qr_code') {
                        $updates['whatsapp_qr_code_base64'] = null;
                    }

                    $user->forceFill($updates)->save();

                    Log::info('Status da instância do WhatsApp atualizado via webhook.', [
                        'user_id' => $user->id,
                        'instance_uuid' => $instanceUuid,
                        'status' => $status,
                    ]);
                }
            }
        }

        return response()->json(['received' => true]);
    }

    private function extractQrCode(Request $request): ?string
    {
        $payload = $request->input('data', []);

        $candidates = [
            Arr::get($payload, 'qr_code_base64'),
            Arr::get($payload, 'qrCodeBase64'),
            Arr::get($payload, 'qr_code'),
            Arr::get($payload, 'qrcode'),
            $request->input('qr_code_base64'),
            $request->input('qrCodeBase64'),
            $request->input('qr_code'),
            $request->input('qrcode'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }
}
