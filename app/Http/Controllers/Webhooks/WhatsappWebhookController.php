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
                    $user->forceFill([
                        'whatsapp_instance_status' => $status,
                    ])->save();

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
}
