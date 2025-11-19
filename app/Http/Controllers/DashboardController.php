<?php

namespace App\Http\Controllers;

use App\Models\MetaMessage;
use App\Models\MetaResposta;
use App\Models\Paciente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();
        Carbon::setLocale(config('app.locale', 'pt_BR'));

        $activeStatuses = ['ativo', 'em_atendimento'];
        $userId = (int) Auth::id();

        $activePatientIds = Paciente::where('user_id', $userId)
            ->whereIn('status', $activeStatuses)
            ->pluck('id');

        $activePatients = Paciente::where('user_id', $userId)
            ->whereIn('status', $activeStatuses)
            ->count();
        $activePatientsPrevious = Paciente::where('user_id', $userId)
            ->whereIn('status', $activeStatuses)
            ->where('created_at', '<=', $now->copy()->subDays(7))
            ->count();
        $activeTrend = $this->makeTrend($activePatientsPrevious, $activePatients);

        $totalMessages = MetaMessage::whereIn('paciente_id', $activePatientIds)
            ->where('data_envio', '<=', $now)
            ->count();
        $respondedMessages = MetaResposta::whereIn('paciente_id', $activePatientIds)->count();
        $averageEngagement = $totalMessages > 0
            ? round(($respondedMessages / $totalMessages) * 100, 1)
            : 0.0;

        $lastWeekStart = $now->copy()->subDays(6)->startOfDay();
        $previousWeekEnd = $lastWeekStart->copy()->subSecond();
        $previousWeekStart = $lastWeekStart->copy()->subDays(7)->startOfDay();

        $lastWeekTotalMessages = MetaMessage::whereIn('paciente_id', $activePatientIds)
            ->whereBetween('data_envio', [$lastWeekStart, $now])
            ->count();
        $lastWeekRespondedMessages = MetaResposta::whereIn('paciente_id', $activePatientIds)
            ->whereBetween('respondido_em', [$lastWeekStart, $now])
            ->count();
        $lastWeekEngagement = $lastWeekTotalMessages > 0
            ? ($lastWeekRespondedMessages / $lastWeekTotalMessages) * 100
            : 0.0;

        $previousWeekTotalMessages = MetaMessage::whereIn('paciente_id', $activePatientIds)
            ->whereBetween('data_envio', [$previousWeekStart, $previousWeekEnd])
            ->count();
        $previousWeekRespondedMessages = MetaResposta::whereIn('paciente_id', $activePatientIds)
            ->whereBetween('respondido_em', [$previousWeekStart, $previousWeekEnd])
            ->count();
        $previousWeekEngagement = $previousWeekTotalMessages > 0
            ? ($previousWeekRespondedMessages / $previousWeekTotalMessages) * 100
            : 0.0;

        $engagementTrend = $this->makeTrend($previousWeekEngagement, $lastWeekEngagement);

        $alertsWeekStart = $lastWeekStart;
        $alertsWeekCount = MetaMessage::where('status', '!=', 'respondido')
            ->whereBetween('data_envio', [$alertsWeekStart, $now])
            ->whereHas('paciente', fn ($query) => $query->where('user_id', $userId))
            ->count();

        $alertsPreviousWeekCount = MetaMessage::where('status', '!=', 'respondido')
            ->whereBetween('data_envio', [$previousWeekStart, $previousWeekEnd])
            ->whereHas('paciente', fn ($query) => $query->where('user_id', $userId))
            ->count();

        $alertsTrend = $this->makeTrend($alertsPreviousWeekCount, $alertsWeekCount, increaseIsPositive: false);

        $alertsData = $this->buildAlertPatients($now, $userId);
        $evolucaoData = $this->buildEvolucaoData($now, $userId);
        $statusDistribution = $this->buildStatusDistribution($userId);
        $recentAlerts = $this->buildRecentAlerts($now, $userId);

        return view('dashboard.index', [
            'stats' => [
                'active_patients' => [
                    'value' => $activePatients,
                    'trend' => $activeTrend,
                ],
                'average_engagement' => [
                    'value' => $averageEngagement,
                    'trend' => $engagementTrend,
                ],
                'alerts_week' => [
                    'value' => $alertsWeekCount,
                    'trend' => $alertsTrend,
                ],
            ],
            'alertSummary' => $alertsData['summary'],
            'alertPatients' => $alertsData['patients'],
            'charts' => [
                'evolucao' => $evolucaoData,
                'status' => $statusDistribution,
            ],
            'recentAlerts' => $recentAlerts,
        ]);
    }

    /**
     * @return array{percent: float, direction: string, is_positive: bool, symbol: string, color: string}
     */
    private function makeTrend(?float $previous, ?float $current, bool $increaseIsPositive = true): array
    {
        $previous = $previous ?? 0.0;
        $current = $current ?? 0.0;

        if ($previous <= 0.0 && $current <= 0.0) {
            return [
                'percent' => 0.0,
                'direction' => 'flat',
                'is_positive' => true,
                'symbol' => '—',
                'color' => 'text-slate-500',
            ];
        }

        if ($previous <= 0.0) {
            $percent = 100.0;
            $direction = 'up';
        } else {
            $percent = (($current - $previous) / $previous) * 100;
            if (abs($percent) < 0.05) {
                return [
                    'percent' => 0.0,
                    'direction' => 'flat',
                    'is_positive' => true,
                    'symbol' => '—',
                    'color' => 'text-slate-500',
                ];
            }
            $direction = $percent > 0 ? 'up' : 'down';
        }

        $isPositive = $increaseIsPositive ? $percent >= 0 : $percent <= 0;
        $symbol = $direction === 'up' ? '▲' : '▼';
        $color = $direction === 'up'
            ? ($isPositive ? 'text-green-600' : 'text-red-600')
            : ($isPositive ? 'text-green-600' : 'text-red-600');

        return [
            'percent' => round(abs($percent), 1),
            'direction' => $direction,
            'is_positive' => $isPositive,
            'symbol' => $symbol,
            'color' => $color,
        ];
    }

    /**
     * @return array{summary: array{critico: int, atencao: int}, patients: Collection<int, array>}
     */
    private function buildAlertPatients(Carbon $now, ?int $userId): array
    {
        $pendingMessages = MetaMessage::with(['paciente', 'meta'])
            ->where('status', '!=', 'respondido')
            ->where('data_envio', '<=', $now)
            ->whereHas('paciente', fn ($query) => $query->where('user_id', $userId))
            ->get();

        $summary = [
            'critico' => 0,
            'atencao' => 0,
        ];

        $patients = $pendingMessages
            ->groupBy('paciente_id')
            ->map(function (Collection $messages) use ($now, &$summary) {
                $first = $messages->first();
                $paciente = $first?->paciente;

                if (! $paciente) {
                    return null;
                }

                $oldest = $messages->sortBy('data_envio')->first();
                $latest = $messages->sortByDesc('data_envio')->first();

                if (! $oldest || ! $oldest->meta) {
                    return null;
                }

                $daysOverdue = $oldest->data_envio->diffInDays($now);
                $severity = $this->severityFromDays($daysOverdue);

                if (! $severity) {
                    return null;
                }

                $summary[$severity]++;

                $contact = $paciente->whatsapp_numero ?: $paciente->telefone;

                return [
                    'paciente' => $paciente,
                    'initial' => $this->makeInitial($paciente->nome),
                    'meta_nome' => $oldest->meta->nome,
                    'descricao' => sprintf(
                        'Meta "%s" pendente há %d dia%s.',
                        $oldest->meta->nome,
                        $daysOverdue,
                        $daysOverdue === 1 ? '' : 's'
                    ),
                    'ultimo_evento' => $latest
                        ? 'Último envio: ' . $latest->data_envio->format('d/m/Y \à\s H:i')
                        : null,
                    'severity' => $severity,
                    'dias_atraso' => $daysOverdue,
                    'telefone' => $contact,
                    'telefone_link' => $this->makePhoneLink($contact),
                ];
            })
            ->filter()
            ->sort(function (array $a, array $b) {
                $order = ['critico' => 0, 'atencao' => 1];
                $severityComparison = ($order[$a['severity']] ?? 99) <=> ($order[$b['severity']] ?? 99);

                if ($severityComparison !== 0) {
                    return $severityComparison;
                }

                return ($b['dias_atraso'] ?? 0) <=> ($a['dias_atraso'] ?? 0);
            })
            ->values()
            ->take(4);

        return [
            'summary' => $summary,
            'patients' => $patients,
        ];
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    private function buildEvolucaoData(Carbon $now, ?int $userId): array
    {
        $months = collect(range(0, 5))->map(function (int $index) use ($now) {
            return $now->copy()->subMonths(5 - $index)->startOfMonth();
        });

        $startDate = $months->first()->copy();

        $responses = MetaResposta::where('respondido_em', '>=', $startDate)
            ->whereHas('paciente', fn ($query) => $query->where('user_id', $userId))
            ->get()
            ->groupBy(fn (MetaResposta $resposta) => $resposta->respondido_em->format('Y-m'))
            ->map(fn (Collection $group) => $group->pluck('paciente_id')->unique()->count());

        $labels = [];
        $values = [];

        foreach ($months as $month) {
            $labels[] = $month->locale('pt_BR')->translatedFormat('M/Y');
            $values[] = (int) ($responses->get($month->format('Y-m')) ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, int>, colors: array<int, string>}
     */
    private function buildStatusDistribution(?int $userId): array
    {
        $statusLabels = [
            'ativo' => 'Ativo',
            'em_atendimento' => 'Em atendimento',
            'inativo' => 'Inativo',
        ];

        $counts = Paciente::where('user_id', $userId)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $values = [];

        foreach ($statusLabels as $status => $label) {
            $labels[] = $label;
            $values[] = (int) ($counts[$status] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'colors' => ['#b6d7a8', '#ffe599', '#f4cccc'],
        ];
    }

    /**
     * @return Collection<int, array{text: string, level: string}>
     */
    private function buildRecentAlerts(Carbon $now, ?int $userId): Collection
    {
        return MetaMessage::with(['meta'])
            ->where('status', '!=', 'respondido')
            ->where('data_envio', '<=', $now)
            ->whereHas('paciente', fn ($query) => $query->where('user_id', $userId))
            ->orderByDesc('data_envio')
            ->get()
            ->map(function (MetaMessage $message) use ($now) {
                $daysOverdue = $message->data_envio->diffInDays($now);
                $severity = $this->severityFromDays($daysOverdue);

                if (! $severity || ! $message->meta) {
                    return null;
                }

                return [
                    'text' => sprintf(
                        '⚠️ %s ainda não respondeu "%s" enviada em %s.',
                        $message->paciente_nome,
                        $message->meta->nome,
                        $message->data_envio->format('d/m/Y \à\s H:i')
                    ),
                    'level' => $severity,
                ];
            })
            ->filter()
            ->values()
            ->take(4);
    }

    private function severityFromDays(int $daysOverdue): ?string
    {
        if ($daysOverdue >= 5) {
            return 'critico';
        }

        if ($daysOverdue >= 2) {
            return 'atencao';
        }

        return null;
    }

    private function makeInitial(?string $name): string
    {
        $trimmed = trim((string) $name);

        if ($trimmed === '') {
            return '?';
        }

        $firstCharacter = mb_substr($trimmed, 0, 1, 'UTF-8');

        return mb_strtoupper($firstCharacter, 'UTF-8');
    }

    private function makePhoneLink(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        return 'tel:' . $digits;
    }
}
