<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class WhatsappService
{
    public function sendDocument(User $user, string $phoneNumber, string $fileName, string $caption, string $pdfContents): void
    {
        $instanceUuid = trim((string) $user->whatsapp_instance_uuid);
        $token = (string) config('services.whatsapp.token');
        $url = (string) config('services.whatsapp.send_media_url');

        if ($instanceUuid === '' || $token === '' || $url === '') {
            throw new RuntimeException('Credenciais da API de WhatsApp não configuradas.');
        }

        $number = preg_replace('/\D+/', '', $phoneNumber);

        if ($number === '') {
            throw new RuntimeException('Número de telefone inválido para envio via WhatsApp.');
        }

        if (! str_starts_with($number, '55')) {
            $number = '55' . $number;
        }

        $sanitizedFileName = sprintf(
            '%s.pdf',
            Str::slug(pathinfo($fileName, PATHINFO_FILENAME) ?: 'relatorio'),
        );

        $storagePath = 'whatsapp/' . $sanitizedFileName;

        Storage::disk('public')->put($storagePath, $pdfContents);

        $mediaUrl = URL::to(Storage::disk('public')->url($storagePath));

        $payload = [
            'token' => $token,
            'uuid' => $instanceUuid,
            'number' => $number,
            'media' => $mediaUrl,
            'mediatype' => 'document',
            'caption' => $caption,
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
