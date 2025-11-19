<?php

namespace App\Services;

use App\Models\Paciente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * @return array{
     *     period: array{start: Carbon, end: Carbon},
     *     patients: Collection<int, Paciente>,
     *     totals: array{pacientes: int, previstas: int, realizadas: int, pendentes: int},
     *     engagement_rate: float,
     *     average_engagement: float,
     *     top_engaged: Collection<int, Paciente>,
     *     lowest_engaged: Collection<int, Paciente>
     * }
     */
    public function buildForPeriod(int $userId, Carbon $start, Carbon $end): array
    {
        $activeStatuses = ['ativo', 'em_atendimento'];

        $patients = Paciente::where('user_id', $userId)
            ->whereIn('status', $activeStatuses)
            ->withCount([
                'metaMessages as metas_previstas' => fn ($query) => $query
                    ->whereBetween('data_envio', [$start, $end]),
                'metaRespostas as metas_realizadas' => fn ($query) => $query
                    ->whereBetween('respondido_em', [$start, $end]),
                'metaMessages as metas_pendentes' => fn ($query) => $query
                    ->where('status', '!=', 'respondido')
                    ->whereBetween('data_envio', [$start, $end]),
            ])
            ->orderBy('nome')
            ->get()
            ->map(function (Paciente $paciente) {
                $previstas = (int) $paciente->metas_previstas;
                $realizadas = (int) $paciente->metas_realizadas;

                $paciente->engajamento_percentual = $previstas > 0
                    ? round(($realizadas / $previstas) * 100, 1)
                    : 0.0;

                return $paciente;
            });

        $totals = [
            'pacientes' => $patients->count(),
            'previstas' => (int) $patients->sum('metas_previstas'),
            'realizadas' => (int) $patients->sum('metas_realizadas'),
            'pendentes' => (int) $patients->sum('metas_pendentes'),
        ];

        $engagementRate = $totals['previstas'] > 0
            ? round(($totals['realizadas'] / $totals['previstas']) * 100, 1)
            : 0.0;

        $averageEngagement = $patients->isNotEmpty()
            ? round($patients->avg('engajamento_percentual'), 1)
            : 0.0;

        $topEngaged = $patients->sortByDesc('engajamento_percentual')->take(5);
        $lowestEngaged = $patients->sortBy('engajamento_percentual')->take(5);

        $period = [
            'start' => $start->copy(),
            'end' => $end->copy(),
        ];

        return [
            'period' => $period,
            'patients' => $patients,
            'totals' => $totals,
            'engagement_rate' => $engagementRate,
            'average_engagement' => $averageEngagement,
            'top_engaged' => $topEngaged,
            'lowest_engaged' => $lowestEngaged,
        ];
    }
}
