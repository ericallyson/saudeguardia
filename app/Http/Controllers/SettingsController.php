<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('settings.index', [
            'user' => $user,
            'qrCode' => session('whatsapp_qr_code'),
            'initialStatus' => session('whatsapp_status'),
            'initialStatusLabel' => $this->humanReadableStatus(session('whatsapp_status')),
            'webhookUrl' => $this->resolveWebhookUrl(),
        ]);
    }

    public function createInstance(Request $request)
    {
        $user = $request->user();

        if ($user->whatsapp_instance_uuid) {
            return redirect()
                ->route('settings.index')
                ->with('status', 'Você já possui uma instância configurada.');
        }

        $payload = [
            'token' => $this->getToken(),
            'name' => $user->name,
            'webhook_url' => $this->resolveWebhookUrl(),
            'self_message_notification' => false,
            'auto_reject_calls' => false,
            'auto_read_messages' => false,
        ];

        $response = Http::acceptJson()->post(
            'https://api-whatsapp.api-alisson.com.br/api/v1/instance/create',
            $payload
        );

        if (! $response->successful()) {
            Log::warning('Erro ao criar instância do WhatsApp', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return redirect()
                ->route('settings.index')
                ->with('error', 'Não foi possível criar a instância. Tente novamente mais tarde.');
        }

        $data = $response->json('data');

        if (! is_array($data) || empty($data['uuid'])) {
            Log::warning('Resposta inesperada ao criar instância do WhatsApp', [
                'user_id' => $user->id,
                'body' => $response->json(),
            ]);

            return redirect()
                ->route('settings.index')
                ->with('error', 'A resposta da API não pôde ser interpretada.');
        }

        $user->forceFill([
            'whatsapp_instance_uuid' => $data['uuid'],
        ])->save();

        return redirect()
            ->route('settings.index')
            ->with('whatsapp_qr_code', Arr::get($data, 'qr_code_base64'))
            ->with('whatsapp_status', Arr::get($data, 'status'))
            ->with('status', 'Instância criada com sucesso! Faça a leitura do QR Code para conectar.');
    }

    public function instanceStatus(Request $request)
    {
        $user = $request->user();

        if (! $user->whatsapp_instance_uuid) {
            return response()->json([
                'status' => null,
                'status_label' => 'Nenhuma instância configurada.',
            ], 404);
        }

        $response = Http::acceptJson()->get(
            'https://api-whatsapp.api-alisson.com.br/api/v1/instance/details',
            [
                'token' => $this->getToken(),
                'uuid' => $user->whatsapp_instance_uuid,
            ]
        );

        if (! $response->successful()) {
            Log::warning('Erro ao consultar status da instância do WhatsApp', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return response()->json([
                'status' => null,
                'status_label' => 'Não foi possível consultar o status da instância.',
            ], 500);
        }

        $data = $response->json('data', []);
        $status = Arr::get($data, 'status');

        return response()->json([
            'status' => $status,
            'status_label' => $this->humanReadableStatus($status),
            'data' => $data,
        ]);
    }

    protected function getToken(): string
    {
        return config('services.whatsapp.token');
    }

    protected function resolveWebhookUrl(): string
    {
        $base = rtrim(config('services.whatsapp.webhook_base', config('app.url')), '/');
        $path = route('webhooks.whatsapp', [], false);
        $path = '/' . ltrim($path, '/');

        return $base . $path;
    }

    protected function humanReadableStatus(?string $status): string
    {
        return match ($status) {
            'connected', 'authenticated' => 'Conectado',
            'qr_code' => 'Aguardando leitura do QR Code',
            'loading', 'connecting' => 'Conectando...',
            'disconnected' => 'Desconectado',
            null, '' => 'Status indisponível',
            default => Str::headline(str_replace('_', ' ', $status)),
        };
    }
}
