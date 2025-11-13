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

        $currentStatus = $user->whatsapp_instance_status;

        return view('settings.index', [
            'user' => $user,
            'qrCode' => $user->whatsapp_qr_code_base64,
            'initialStatus' => $currentStatus,
            'initialStatusLabel' => $this->humanReadableStatus($currentStatus),
            'initialIsConnected' => $this->isConnectedStatus($currentStatus),
            'connectedStatuses' => $this->connectedStatuses(),
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

        $data = $this->createRemoteInstanceForUser($user);

        if (! $data) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Não foi possível criar a instância. Tente novamente mais tarde.');
        }

        return redirect()
            ->route('settings.index')
            ->with('status', 'Instância criada com sucesso! Faça a leitura do QR Code para conectar.');
    }

    public function connectInstance(Request $request)
    {
        $user = $request->user();

        if (! $user->whatsapp_instance_uuid) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Nenhuma instância configurada para este usuário.');
        }

        $payload = [
            'token' => $this->getToken(),
            'uuid' => $user->whatsapp_instance_uuid,
        ];

        $response = Http::acceptJson()->post(
            'https://api-whatsapp.api-alisson.com.br/api/v1/instance/connect',
            $payload
        );

        if (! $response->successful()) {
            Log::warning('Erro ao conectar instância do WhatsApp', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return redirect()
                ->route('settings.index')
                ->with('error', 'Não foi possível solicitar a conexão da instância. Tente novamente mais tarde.');
        }

        $data = $response->json('data', []);
        $status = Arr::get($data, 'status');

        $user->forceFill([
            'whatsapp_instance_status' => is_string($status) ? $status : 'connecting',
            'whatsapp_qr_code_base64' => null,
        ])->save();

        return redirect()
            ->route('settings.index')
            ->with('status', 'Solicitação de conexão enviada. Aguarde a atualização do status.');
    }

    public function disconnectInstance(Request $request)
    {
        $user = $request->user();

        if (! $user->whatsapp_instance_uuid) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Nenhuma instância configurada para este usuário.');
        }

        $payload = [
            'token' => $this->getToken(),
            'uuid' => $user->whatsapp_instance_uuid,
        ];

        $response = Http::acceptJson()->post(
            'https://api-whatsapp.api-alisson.com.br/api/v1/instance/disconnect',
            $payload
        );

        if (! $response->successful()) {
            Log::warning('Erro ao desconectar instância do WhatsApp', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return redirect()
                ->route('settings.index')
                ->with('error', 'Não foi possível solicitar a desconexão da instância. Tente novamente mais tarde.');
        }

        $data = $response->json('data', []);
        $status = Arr::get($data, 'status');

        $user->forceFill([
            'whatsapp_instance_status' => is_string($status) ? $status : 'disconnected',
            'whatsapp_qr_code_base64' => null,
        ])->save();

        return redirect()
            ->route('settings.index')
            ->with('status', 'Solicitação de desconexão enviada. Aguarde a atualização do status.');
    }

    public function deleteInstance(Request $request)
    {
        $user = $request->user();

        if (! $user->whatsapp_instance_uuid) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Nenhuma instância configurada para este usuário.');
        }

        $deleted = $this->deleteRemoteInstance($user->whatsapp_instance_uuid, $user->id);

        if (! $deleted) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Não foi possível excluir a instância. Tente novamente mais tarde.');
        }

        $user->forceFill([
            'whatsapp_instance_uuid' => null,
            'whatsapp_instance_status' => null,
            'whatsapp_qr_code_base64' => null,
        ])->save();

        return redirect()
            ->route('settings.index')
            ->with('status', 'Instância excluída com sucesso. Você pode criar uma nova sempre que precisar.');
    }

    public function instanceStatus(Request $request)
    {
        $user = $request->user();

        if (! $user->whatsapp_instance_uuid) {
            return response()->json([
                'status' => null,
                'status_label' => 'Nenhuma instância configurada.',
                'connected' => false,
            ], 404);
        }

        $regenerated = false;

        if ($this->shouldRecreateInstance($user)) {
            $regenerated = $this->recreateInstanceForUser($user);
            $user->refresh();
        }

        if (! $user->whatsapp_instance_uuid) {
            return response()->json([
                'status' => null,
                'status_label' => 'Instância indisponível. Aguarde enquanto recriamos sua conexão.',
                'connected' => false,
            ], 404);
        }

        return response()->json([
            'status' => $user->whatsapp_instance_status,
            'status_label' => $this->humanReadableStatus($user->whatsapp_instance_status),
            'connected' => $this->isConnectedStatus($user->whatsapp_instance_status),
            'qr_code_base64' => $user->whatsapp_qr_code_base64,
            'regenerated' => $regenerated,
        ]);
    }

    protected function getToken(): string
    {
        return config('services.whatsapp.token');
    }

    protected function resolveWebhookUrl(): string
    {
        $explicit = trim((string) config('services.whatsapp.webhook_url'));

        if ($explicit !== '') {
            return rtrim($explicit, '/');
        }

        $base = rtrim(config('services.whatsapp.webhook_base', config('app.url')), '/');
        $path = '/' . ltrim(route('webhooks.whatsapp', [], false), '/');

        return $base . $path;
    }

    protected function humanReadableStatus(?string $status): string
    {
        return match ($status) {
            'connected', 'authenticated', 'open' => 'Conectado',
            'qr_code', 'qrcode' => 'Aguardando leitura do QR Code',
            'loading', 'connecting', 'reconnecting' => 'Conectando...',
            'disconnecting' => 'Desconectando...',
            'disconnected', 'close' => 'Desconectado',
            'refused' => 'Conexão recusada',
            null, '' => 'Status indisponível',
            default => Str::headline(str_replace('_', ' ', $status)),
        };
    }

    protected function connectedStatuses(): array
    {
        return ['connected', 'authenticated', 'open'];
    }

    protected function isConnectedStatus(?string $status): bool
    {
        if (! $status) {
            return false;
        }

        return in_array($status, $this->connectedStatuses(), true);
    }

    protected function shouldRecreateInstance($user): bool
    {
        if (! $user->whatsapp_instance_uuid) {
            return false;
        }

        $status = (string) Str::of((string) $user->whatsapp_instance_status)->lower();

        return in_array($status, ['close', 'refused'], true);
    }

    protected function recreateInstanceForUser($user): bool
    {
        $previousUuid = $user->whatsapp_instance_uuid;

        $user->forceFill([
            'whatsapp_instance_uuid' => null,
            'whatsapp_instance_status' => null,
            'whatsapp_qr_code_base64' => null,
        ])->save();

        if ($previousUuid) {
            $this->deleteRemoteInstance($previousUuid, $user->id);
        }

        return (bool) $this->createRemoteInstanceForUser($user);
    }

    protected function deleteRemoteInstance(string $uuid, ?int $userId = null): bool
    {
        $payload = [
            'token' => $this->getToken(),
            'uuid' => $uuid,
        ];

        $response = Http::acceptJson()->post(
            'https://api-whatsapp.api-alisson.com.br/api/v1/instance/delete',
            $payload
        );

        if (! $response->successful()) {
            Log::warning('Erro ao excluir instância do WhatsApp', [
                'user_id' => $userId,
                'uuid' => $uuid,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    protected function createRemoteInstanceForUser($user): ?array
    {
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

            return null;
        }

        $data = $response->json('data');

        if (! is_array($data) || empty($data['uuid'])) {
            Log::warning('Resposta inesperada ao criar instância do WhatsApp', [
                'user_id' => $user->id,
                'body' => $response->json(),
            ]);

            return null;
        }

        $user->forceFill([
            'whatsapp_instance_uuid' => $data['uuid'],
            'whatsapp_instance_status' => Arr::get($data, 'status'),
            'whatsapp_qr_code_base64' => Arr::get($data, 'qr_code_base64'),
        ])->save();

        return $data;
    }
}
