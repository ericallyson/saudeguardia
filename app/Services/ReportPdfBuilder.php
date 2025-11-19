<?php

namespace App\Services;

use App\Support\SimplePdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportPdfBuilder
{
    public function __construct(private readonly SimplePdf $pdf)
    {
    }

    /**
     * @param array{
     *     period: array{start: Carbon, end: Carbon},
     *     totals: array{pacientes: int, previstas: int, realizadas: int, pendentes: int},
     *     engagement_rate: float,
     *     average_engagement: float,
     *     top_engaged: Collection<int, \App\Models\Paciente>,
     *     lowest_engaged: Collection<int, \App\Models\Paciente>
     * } $report
     */
    public function build(array $report): string
    {
        $lines = [];

        $lines[] = 'Período: ' . $report['period']['start']->format('d/m/Y') . ' a ' . $report['period']['end']->format('d/m/Y');
        $lines[] = sprintf('Total de pacientes ativos: %d', $report['totals']['pacientes']);
        $lines[] = sprintf('Metas previstas: %d', $report['totals']['previstas']);
        $lines[] = sprintf('Metas realizadas: %d', $report['totals']['realizadas']);
        $lines[] = sprintf('Metas pendentes: %d', $report['totals']['pendentes']);
        $lines[] = sprintf('Engajamento geral no período: %.1f%%', $report['engagement_rate']);
        $lines[] = sprintf('Engajamento médio por paciente: %.1f%%', $report['average_engagement']);
        $lines[] = '';

        $lines[] = 'Pacientes com maior engajamento:';
        if ($report['top_engaged']->isEmpty()) {
            $lines[] = '• Nenhum paciente com dados para o período.';
        } else {
            $report['top_engaged']->each(function ($paciente, int $index) use (&$lines) {
                $lines[] = sprintf(
                    '%d. %s — %.1f%% de engajamento (%d/%d)',
                    $index + 1,
                    $paciente->nome,
                    $paciente->engajamento_percentual,
                    (int) $paciente->metas_realizadas,
                    (int) max(1, $paciente->metas_previstas),
                );
            });
        }

        $lines[] = '';
        $lines[] = 'Pacientes que precisam de atenção:';
        if ($report['lowest_engaged']->isEmpty()) {
            $lines[] = '• Nenhum paciente com dados para o período.';
        } else {
            $report['lowest_engaged']->each(function ($paciente, int $index) use (&$lines) {
                $lines[] = sprintf(
                    '%d. %s — %.1f%% de engajamento (%d/%d) e %d metas pendentes',
                    $index + 1,
                    $paciente->nome,
                    $paciente->engajamento_percentual,
                    (int) $paciente->metas_realizadas,
                    (int) max(1, $paciente->metas_previstas),
                    (int) $paciente->metas_pendentes,
                );
            });
        }

        $lines[] = '';
        $lines[] = 'Relatório consolidado em ' . Carbon::now()->format('d/m/Y \à\s H:i');

        return $this->pdf->generate('Relatório Geral — Saúde Guardiã', $lines);
    }
}
