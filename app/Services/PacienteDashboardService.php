<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\Paciente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PacienteDashboardService
{
    public function calcularEngajamento(Paciente $paciente): array
    {
        $agora = Carbon::now();

        $totalInteracoes = $paciente->metaMessages()->count();
        $interacoesConcluidas = $paciente->metaRespostas()->count();
        $previstasAteHoje = $paciente->metaMessages()
            ->where('data_envio', '<=', $agora)
            ->count();

        $percentualTotal = $totalInteracoes > 0
            ? round(($interacoesConcluidas / $totalInteracoes) * 100, 2)
            : 0.0;

        $percentualPrevisto = $previstasAteHoje > 0
            ? round(min(100, ($interacoesConcluidas / $previstasAteHoje) * 100), 2)
            : 0.0;

        return [
            'total_interacoes' => $totalInteracoes,
            'interacoes_concluidas' => $interacoesConcluidas,
            'previstas_ate_hoje' => $previstasAteHoje,
            'percentual_total' => $percentualTotal,
            'percentual_previsto' => $percentualPrevisto,
        ];
    }

    public function calcularAndamentoTratamento(Paciente $paciente): array
    {
        $agora = Carbon::now();

        $inicio = $paciente->data_inicio?->copy();
        if (! $inicio) {
            $primeiraMensagem = $paciente->metaMessages()->min('data_envio');
            $inicio = $primeiraMensagem ? Carbon::parse($primeiraMensagem) : null;
        }

        $vencimentoMaximo = $paciente->metas()->whereNotNull('meta_paciente.vencimento')->max('meta_paciente.vencimento');
        $fim = $vencimentoMaximo ? Carbon::parse($vencimentoMaximo) : null;

        if (! $fim) {
            $ultimaMensagem = $paciente->metaMessages()->max('data_envio');
            $fim = $ultimaMensagem ? Carbon::parse($ultimaMensagem) : null;
        }

        if (! $inicio || ! $fim) {
            return [
                'inicio' => $inicio,
                'fim' => $fim,
                'dias_totais' => 0,
                'dias_passados' => 0,
                'percentual_passado' => 0.0,
                'percentual_futuro' => 100.0,
            ];
        }

        if ($inicio->greaterThan($fim)) {
            $fim = $inicio->copy();
        }

        $inicio = $inicio->copy()->startOfDay();
        $fim = $fim->copy()->endOfDay();

        $totalDias = max(1, $inicio->diffInDays($fim) + 1);

        if ($agora->lessThan($inicio)) {
            $diasPassados = 0;
        } elseif ($agora->greaterThan($fim)) {
            $diasPassados = $totalDias;
        } else {
            $diasPassados = $inicio->diffInDays($agora) + 1;
        }

        $diasPassados = min($diasPassados, $totalDias);

        $percentualPassado = round(min(100, ($diasPassados / $totalDias) * 100), 2);
        $percentualFuturo = round(max(0, 100 - $percentualPassado), 2);

        return [
            'inicio' => $inicio,
            'fim' => $fim,
            'dias_totais' => $totalDias,
            'dias_passados' => $diasPassados,
            'percentual_passado' => $percentualPassado,
            'percentual_futuro' => $percentualFuturo,
        ];
    }

    /**
     * @return Collection<int, \App\Models\MetaMessage>
     */
    public function listarMetasFuturas(Paciente $paciente): Collection
    {
        return $paciente->metaMessages()
            ->with('meta')
            ->where('data_envio', '>', Carbon::now())
            ->where('status', '!=', 'respondido')
            ->orderBy('data_envio')
            ->get();
    }

    public function construirGraficosMetas(Paciente $paciente, ?Meta $metaSelecionada = null): Collection
    {
        $agora = Carbon::now()->endOfDay();

        $metas = $paciente->metas()
            ->when($metaSelecionada, fn ($query) => $query->where('metas.id', $metaSelecionada->id))
            ->with([
                'respostas' => function ($query) use ($paciente) {
                    $query->where('paciente_id', $paciente->id)->orderBy('respondido_em');
                },
                'messages' => function ($query) use ($paciente) {
                    $query->where('paciente_id', $paciente->id)->orderBy('data_envio');
                },
            ])
            ->orderBy('metas.nome')
            ->get();

        return $metas
            ->map(fn (Meta $meta) => $this->buildMetaChart($meta, $agora))
            ->filter()
            ->values();
    }

    private function buildMetaChart(Meta $meta, Carbon $agora): ?array
    {
        $respostas = $meta->respostas
            ->filter(fn ($resposta) => $resposta->respondido_em !== null)
            ->sortBy('respondido_em');

        $mensagens = $meta->messages->sortBy('data_envio');

        $inicio = collect([
            $mensagens->first()?->data_envio?->copy()->startOfDay(),
            $respostas->first()?->respondido_em?->copy()->startOfDay(),
        ])
            ->filter()
            ->sortBy(fn (Carbon $data) => $data->timestamp)
            ->first();

        if (! $inicio) {
            $inicio = $agora->copy()->startOfDay();
        }

        if ($inicio->greaterThan($agora)) {
            $inicio = $agora->copy()->startOfDay();
        }

        $respostasPorDia = $respostas
            ->groupBy(fn ($resposta) => $resposta->respondido_em->format('Y-m-d'));

        $possuiDados = $respostasPorDia->isNotEmpty();
        $ehNumerica = in_array($meta->tipo, ['integer', 'scale'], true);

        $labels = [];
        $labelsCompletos = [];
        $valores = [];
        $cores = [];

        for ($dia = $inicio->copy(); $dia->lte($agora); $dia->addDay()) {
            $chaveDia = $dia->format('Y-m-d');
            $labels[] = $dia->format('d/m');
            $labelsCompletos[] = $dia->format('d/m/Y');

            if ($ehNumerica) {
                $valorDia = $respostasPorDia->has($chaveDia)
                    ? (float) $respostasPorDia->get($chaveDia)->sortBy('respondido_em')->last()->valor
                    : null;
                $valores[] = $valorDia;
            } else {
                $preencheu = $respostasPorDia->has($chaveDia);
                $valores[] = $preencheu ? 1 : 0;
                $cores[] = $preencheu ? '#34d399' : '#f87171';
            }
        }

        return [
            'meta_id' => $meta->id,
            'nome' => $meta->nome,
            'tipo' => $meta->tipo,
            'has_data' => $possuiDados,
            'chart' => array_filter([
                'type' => $ehNumerica ? 'line' : 'bar',
                'labels' => $labels,
                'fullLabels' => $labelsCompletos,
                'values' => $valores,
                'colors' => $ehNumerica ? null : $cores,
                'datasetLabel' => $ehNumerica ? 'Valor informado' : 'Preenchimento diário',
            ]),
        ];
    }
}
