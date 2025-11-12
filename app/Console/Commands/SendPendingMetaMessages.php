<?php

namespace App\Console\Commands;

use App\Models\MetaMessage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendPendingMetaMessages extends Command
{
    protected $signature = 'metas:dispatch-pending {--once : Executa apenas uma verificação} {--no-delay : Não aguardar entre os envios (útil para testes)}';

    protected $description = 'Verifica e envia mensagens de metas pendentes via WhatsApp.';

    public function handle(): int
    {
        $this->info('Monitoramento de mensagens iniciado.');

        $runContinuously = ! $this->option('once');

        do {
            $processed = $this->dispatchPendingMessages();

            if ($processed === 0) {
                $this->info('Nenhuma mensagem pendente encontrada neste ciclo.');
            } else {
                $this->info(sprintf('%d mensagem(ns) enviada(s) neste ciclo.', $processed));
            }

            if ($runContinuously) {
                sleep(3600);
            }
        } while ($runContinuously);

        return Command::SUCCESS;
    }

    private function dispatchPendingMessages(): int
    {
        $now = Carbon::now();

        $instanceUuid = $this->resolveWhatsappInstanceUuid();

        if ($instanceUuid === null) {
            Log::warning('Nenhuma instância de WhatsApp configurada para envio.');

            return 0;
        }

        $messages = MetaMessage::query()
            ->where('status', 'pendente')
            ->where('data_envio', '<=', $now)
            ->orderBy('data_envio')
            ->get()
            ->values();

        $total = $messages->count();

        if ($total === 0) {
            return 0;
        }

        $processed = 0;

        foreach ($messages as $index => $message) {
            if ($this->sendMessage($message, $instanceUuid)) {
                $processed++;
            }

            if ($this->option('no-delay') || $index === $total - 1) {
                continue;
            }

            $delay = random_int(10, 45);
            $this->info(sprintf('Aguardando %d segundo(s) antes do próximo envio.', $delay));
            sleep($delay);
        }

        return $processed;
    }

    private function sendMessage(MetaMessage $message, string $instanceUuid): bool
    {
        $url = (string) config('services.whatsapp.send_text_url');
        $token = (string) config('services.whatsapp.token');
        $instanceUuid = trim($instanceUuid);

        if ($url === '' || $token === '' || $instanceUuid === '') {
            Log::error('Credenciais da API de WhatsApp ausentes.');

            return false;
        }

        $number = preg_replace('/\D+/', '', (string) $message->telefone);

        if ($number === '') {
            Log::warning('Telefone inválido para envio de mensagem de meta.', [
                'meta_message_id' => $message->id,
            ]);

            return false;
        }

        $payload = [
            'token' => $token,
            'uuid' => $instanceUuid,
            'number' => $number,
            'content' => $this->buildContent($message),
            'delay' => 2500,
        ];

        try {
            $response = Http::timeout(30)->asJson()->post($url, $payload);
        } catch (Throwable $exception) {
            Log::error('Erro ao enviar mensagem de meta.', [
                'meta_message_id' => $message->id,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::error('Falha ao enviar mensagem de meta.', [
                'meta_message_id' => $message->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        $message->forceFill([
            'status' => 'enviado',
            'enviado_em' => Carbon::now(),
        ])->save();

        return true;
    }

    private function buildContent(MetaMessage $message): string
    {
        return sprintf(
            "Oi %s.\nChegou a hora de informar os dados da sua meta.\nAcesse: %s\n\nIsso é muito importante para alcançar os resultados do seu tratamento!\n",
            $message->paciente_nome,
            $message->link,
        );
    }

    private function resolveWhatsappInstanceUuid(): ?string
    {
        return User::query()
            ->whereNotNull('whatsapp_instance_uuid')
            ->orderByDesc('updated_at')
            ->value('whatsapp_instance_uuid');
    }
}
