<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class WhatsappService
{
    public function sendDocument(User $user, string $phoneNumber, string $fileName, string $caption, string $pdfContents): void
    {
        $instanceUuid = trim((string) $user->whatsapp_instance_uuid);
        $token = (string) config('services.whatsapp.token');
        $url = (string) config('services.whatsapp.send_document_url');

        if ($instanceUuid === '' || $token === '' || $url === '') {
            throw new RuntimeException('Credenciais da API de WhatsApp não configuradas.');
        }

        $number = preg_replace('/\D+/', '', $phoneNumber);

        if ($number === '') {
            throw new RuntimeException('Número de telefone inválido para envio via WhatsApp.');
        }

        $payload = [
            'token' => $token,
            'uuid' => $instanceUuid,
            'number' => $number,
            'fileName' => $fileName,
            'caption' => $caption,
            'mimetype' => 'application/pdf',
            'document' => base64_encode($pdfContents),
        ];

        try {
            $response = Http::timeout(30)->asJson()->post($url, $payload);
        } catch (Throwable $exception) {
            throw new RuntimeException('Erro na comunicação com a API de WhatsApp: ' . $exception->getMessage(), 0, $exception);
        }

        if (! $response->successful()) {
            Log::error('Falha ao enviar documento via WhatsApp.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('A API de WhatsApp retornou uma resposta inválida.');
        }
    }
}
