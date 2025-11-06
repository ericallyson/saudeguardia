<?php

namespace App\Services;

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
}
