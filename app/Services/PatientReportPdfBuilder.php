<?php

namespace App\Services;

use App\Models\MetaMessage;
use App\Models\Paciente;
use App\Support\SimplePdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PatientReportPdfBuilder
{
    public function __construct(private readonly SimplePdf $pdf)
    {
    }

    /**
     * @param array{total_interacoes:int, interacoes_concluidas:int, previstas_ate_hoje:int, percentual_total:float, percentual_previsto:float} $engajamento
     * @param array{inicio:?Carbon, fim:?Carbon, dias_totais:int, dias_passados:int, percentual_passado:float, percentual_futuro:float} $andamento
     * @param Collection<int, MetaMessage> $metasFuturas
     */
    public function build(
        Paciente $paciente,
        array $engajamento,
        array $andamento,
        Collection $metasFuturas,
        string $reportUrl,
    ): string {
        $lines = [];
        $lines[] = 'Paciente: ' . $paciente->nome;
        $lines[] = 'Plano de acompanhamento: ' . ($paciente->plano ?: 'Não informado');
        $lines[] = 'Início do acompanhamento: ' . ($paciente->data_inicio?->format('d/m/Y') ?: 'Não informado');
        $lines[] = '';

        $lines[] = 'Engajamento nas metas';
        $lines[] = sprintf('• Metas previstas até hoje: %d', max(0, (int) $engajamento['previstas_ate_hoje']));
        $lines[] = sprintf('• Metas respondidas: %d', max(0, (int) $engajamento['interacoes_concluidas']));
        $lines[] = sprintf('• Percentual de respostas até hoje: %.2f%%', (float) $engajamento['percentual_previsto']);
        $lines[] = '';

        $lines[] = 'Andamento do tratamento';
        $lines[] = sprintf('• Total de dias previstos: %d', max(0, (int) $andamento['dias_totais']));
        $lines[] = sprintf('• Dias concluídos: %d', max(0, (int) $andamento['dias_passados']));
        $lines[] = sprintf('• Conclusão estimada: %s', $andamento['fim'] instanceof Carbon ? $andamento['fim']->format('d/m/Y') : 'Não estimado');
        $lines[] = sprintf('• Progresso estimado: %.2f%% concluído', (float) $andamento['percentual_passado']);
        $lines[] = '';

        $lines[] = 'Próximos acompanhamentos agendados';

        if ($metasFuturas->isEmpty()) {
            $lines[] = '• Não há acompanhamentos futuros agendados.';
        } else {
            $metasFuturas
                ->sortBy('data_envio')
                ->take(10)
                ->values()
                ->each(function (MetaMessage $metaMessage, int $index) use (&$lines): void {
                    $lines[] = sprintf(
                        '%d. %s às %s — %s',
                        $index + 1,
                        $metaMessage->data_envio->format('d/m/Y'),
                        $metaMessage->data_envio->format('H:i'),
                        $metaMessage->meta?->nome ?? 'Meta sem nome',
                    );
                });
        }

        $lines[] = '';
        $lines[] = 'Acesse o painel completo para acompanhar todos os detalhes:';
        $lines[] = $reportUrl;
        $lines[] = '';
        $lines[] = 'Relatório gerado em ' . Carbon::now()->format('d/m/Y \à\s H:i');

        return $this->pdf->generate('Relatório de Acompanhamento — Saúde Guardiã', $lines);
    }
}
