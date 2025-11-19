<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\Paciente;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PacienteDashboardService
{
    private const BLOOD_PRESSURE_LEVELS = [
        'normal' => [
            'label' => 'Pressão normal',
            'description' => 'PAS < 120 e PAD < 80',
            'color' => '#22c55e',
        ],
        'pre' => [
            'label' => 'Pré-hipertenso',
            'description' => 'PAS entre 120-139 e/ou PAD entre 80-89',
            'color' => '#fb923c',
        ],
        'stage_1' => [
            'label' => 'Hipertensão Estágio 1',
            'description' => 'PAS 140-159 e/ou PAD 90-99',
            'color' => '#f87171',
        ],
        'stage_2' => [
            'label' => 'Hipertensão Estágio 2',
            'description' => 'PAS 160-179 e/ou PAD 100-109',
            'color' => '#ef4444',
        ],
        'stage_3' => [
            'label' => 'Hipertensão Estágio 3',
            'description' => 'PAS ≥ 180 e/ou PAD ≥ 110',
            'color' => '#b91c1c',
        ],
    ];

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
            'dias_totais' => (int) round($totalDias),
            'dias_passados' => (int) round($diasPassados),
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
            ->get()
            ->unique('id')
            ->values();

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

        if ($meta->tipo === 'blood_pressure') {
            $bloodPressureChart = $this->buildBloodPressureChart($respostas);

            return [
                'meta_id' => $meta->id,
                'nome' => $meta->nome,
                'tipo' => $meta->tipo,
                'has_data' => $bloodPressureChart['has_data'],
                'legend' => $bloodPressureChart['legend'],
                'chart' => $bloodPressureChart['chart'],
            ];
        }

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
            'legend' => null,
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

    private function buildBloodPressureChart(Collection $respostas): array
    {
        $points = $respostas
            ->map(function ($resposta) {
                $valores = $this->parseBloodPressureValue($resposta->valor);

                if (! $valores) {
                    return null;
                }

                $classificacao = $this->classifyBloodPressure($valores['pas'], $valores['pad']);

                return [
                    'pas' => $valores['pas'],
                    'pad' => $valores['pad'],
                    'color' => $classificacao['color'],
                    'category' => $classificacao['label'],
                    'label' => $resposta->respondido_em?->format('d/m'),
                    'fullLabel' => $resposta->respondido_em?->format('d/m/Y \à\s H:i'),
                    'valueLabel' => sprintf('%d x %d', $valores['pas'], $valores['pad']),
                ];
            })
            ->filter()
            ->values();

        return [
            'has_data' => $points->isNotEmpty(),
            'legend' => array_values(self::BLOOD_PRESSURE_LEVELS),
            'chart' => [
                'type' => 'blood_pressure',
                'points' => $points,
                'axis' => [
                    'pas' => ['min' => 50, 'max' => 220],
                    'pad' => ['min' => 50, 'max' => 120],
                ],
                'scaleZones' => $this->bloodPressureScaleZonesForChart(),
                'datasetLabel' => 'Medições (PAS x PAD)',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function bloodPressureScaleZonesForChart(): array
    {
        return array_map(function ($zone) {
            $chartZone = [
                'label' => $zone['label'],
                'backgroundColor' => $zone['backgroundColor'],
                'borderColor' => $zone['borderColor'],
            ];

            if (isset($zone['pad'])) {
                $chartZone['x'] = $zone['pad'];
            }

            if (isset($zone['pas'])) {
                $chartZone['y'] = $zone['pas'];
            }

            return $chartZone;
        }, $this->bloodPressureScaleZones());
    }

    private function parseBloodPressureValue(?string $valor): ?array
    {
        if (! is_string($valor)) {
            return null;
        }

        if (! preg_match('/^(\d{2,3})\s*[xX]\s*(\d{2,3})$/', trim($valor), $matches)) {
            return null;
        }

        $pas = (int) $matches[1];
        $pad = (int) $matches[2];

        if ($pas < $pad) {
            [$pas, $pad] = [$pad, $pas];
        }

        return [
            'pas' => $pas,
            'pad' => $pad,
        ];
    }

    private function classifyBloodPressure(int $pas, int $pad): array
    {
        if ($pas >= 180 || $pad >= 110) {
            return self::BLOOD_PRESSURE_LEVELS['stage_3'];
        }

        if ($pas >= 160 || $pad >= 100) {
            return self::BLOOD_PRESSURE_LEVELS['stage_2'];
        }

        if ($pas >= 140 || $pad >= 90) {
            return self::BLOOD_PRESSURE_LEVELS['stage_1'];
        }

        if ($pas > 120 || $pad > 80) {
            return self::BLOOD_PRESSURE_LEVELS['pre'];
        }

        return self::BLOOD_PRESSURE_LEVELS['normal'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function bloodPressureScaleZones(): array
    {
        return [
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['normal']['label'],
                'pas' => ['min' => 50, 'max' => 120],
                'pad' => ['min' => 50, 'max' => 80],
                'backgroundColor' => 'rgba(22, 163, 74, 0.28)',
                'borderColor' => 'rgba(21, 128, 61, 0.85)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['pre']['label'],
                'pas' => ['min' => 120, 'max' => 140],
                'backgroundColor' => 'rgba(249, 115, 22, 0.25)',
                'borderColor' => 'rgba(234, 88, 12, 0.8)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['pre']['label'],
                'pad' => ['min' => 80, 'max' => 90],
                'backgroundColor' => 'rgba(249, 115, 22, 0.25)',
                'borderColor' => 'rgba(234, 88, 12, 0.8)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['stage_1']['label'],
                'pas' => ['min' => 140, 'max' => 160],
                'backgroundColor' => 'rgba(239, 68, 68, 0.24)',
                'borderColor' => 'rgba(220, 38, 38, 0.82)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['stage_1']['label'],
                'pad' => ['min' => 90, 'max' => 100],
                'backgroundColor' => 'rgba(239, 68, 68, 0.24)',
                'borderColor' => 'rgba(220, 38, 38, 0.82)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['stage_2']['label'],
                'pas' => ['min' => 160, 'max' => 180],
                'backgroundColor' => 'rgba(220, 38, 38, 0.28)',
                'borderColor' => 'rgba(185, 28, 28, 0.9)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['stage_2']['label'],
                'pad' => ['min' => 100, 'max' => 110],
                'backgroundColor' => 'rgba(220, 38, 38, 0.28)',
                'borderColor' => 'rgba(185, 28, 28, 0.9)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['stage_3']['label'],
                'pas' => ['min' => 180, 'max' => 220],
                'backgroundColor' => 'rgba(153, 27, 27, 0.3)',
                'borderColor' => 'rgba(127, 29, 29, 0.95)',
            ],
            [
                'label' => self::BLOOD_PRESSURE_LEVELS['stage_3']['label'],
                'pad' => ['min' => 110, 'max' => 140],
                'backgroundColor' => 'rgba(153, 27, 27, 0.3)',
                'borderColor' => 'rgba(127, 29, 29, 0.95)',
            ],
        ];
    }
}
