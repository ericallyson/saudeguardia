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

        $instanceUuid = $request->input('instance_uuid');
        $status = $this->extractStatus($request);
        $user = null;

        if (! $instanceUuid) {
            Log::warning('Webhook recebido sem UUID da instância.');
        } else {
            $user = User::where('whatsapp_instance_uuid', $instanceUuid)->first();

            if (! $user) {
                Log::warning('Webhook recebido para instância desconhecida.', [
                    'instance_uuid' => $instanceUuid,
                ]);
            }
        }

        $updates = [];

        if (is_string($status) && $status !== '') {
            $updates['whatsapp_instance_status'] = $status;
        } elseif ($instanceUuid) {
            Log::warning('Webhook recebido sem status válido.', [
                'instance_uuid' => $instanceUuid,
            ]);
        }

        if ($request->input('event') === 'connection.update') {
            $qrCode = $this->extractQrCode($request);

            if ($qrCode !== null) {
                $updates['whatsapp_qr_code_base64'] = $qrCode;
            } elseif (isset($updates['whatsapp_instance_status']) && $updates['whatsapp_instance_status'] !== 'qr_code') {
                $updates['whatsapp_qr_code_base64'] = null;
            } elseif (is_string($status) && $status !== '' && $status !== 'qr_code') {
                $updates['whatsapp_qr_code_base64'] = null;
            }
        }

        if ($user && $updates !== []) {
            $user->forceFill($updates)->save();

            Log::info('Dados da instância do WhatsApp atualizados via webhook.', [
                'user_id' => $user->id,
                'instance_uuid' => $instanceUuid,
                'status' => $status,
                'updates' => array_keys($updates),
            ]);
        }

        return response()->json(['received' => true]);
    }

    private function extractStatus(Request $request): ?string
    {
        $payload = $request->input('data', []);

        $candidates = [
            Arr::get($payload, 'status'),
            $request->input('status'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
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
