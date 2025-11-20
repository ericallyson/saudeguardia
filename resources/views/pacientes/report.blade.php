<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Acompanhamento — {{ $paciente->nome }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8f5f1;
            --card: #ffffff;
            --text: #2d3a4d;
            --muted: #6b7280;
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --accent: #10b981;
            --amber: #f59e0b;
            --border: #e3d7c3;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }

        header {
            background: var(--card);
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        header img { height: 48px; }

        .container { max-width: 1080px; margin: 0 auto; padding: 24px; }

        h1 { margin: 0; font-size: 28px; }
        h2 { font-size: 20px; margin: 0 0 12px; }
        p { margin: 4px 0; }

        .grid { display: grid; gap: 18px; }
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.05);
            border: 1px solid rgba(227, 215, 195, 0.7);
        }

        .tag { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--muted); }
        .pill { background: var(--primary-light); color: var(--primary); padding: 6px 10px; border-radius: 999px; font-weight: 600; font-size: 12px; }

        .progress {
            position: relative;
            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress .bar { height: 100%; border-radius: inherit; }

        .muted { color: var(--muted); font-size: 14px; }

        .chart-card canvas { width: 100%; height: 220px; }

        .observation {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 12px;
            padding: 12px 14px;
            color: #065f46;
        }

        .footer { text-align: center; color: var(--muted); font-size: 13px; margin-top: 24px; }
    </style>
</head>
<body>
<header>
    <img src="https://app.saudeguardia.com.br/img/logo-horizontal.png" alt="Saúde Guardiã">
    <div>
        <h1>Relatório de Acompanhamento</h1>
        <p><strong>Paciente:</strong> {{ $paciente->nome }}</p>
        <p><strong>Plano:</strong> {{ $paciente->plano ?? 'Não informado' }}</p>
        <p><strong>Início:</strong> {{ $paciente->data_inicio?->format('d/m/Y') ?? 'Não informado' }}</p>
    </div>
</header>

<main class="container">
    <div class="grid grid-2">
        <section class="card">
            <div class="flex items-center justify-between" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                <h2>Engajamento nas metas</h2>
                <span class="pill">{{ number_format($engajamento['percentual_previsto'], 2, ',', '.') }}%</span>
            </div>
            <p class="muted">{{ $engajamento['interacoes_concluidas'] }} de {{ max(1, $engajamento['previstas_ate_hoje']) }} metas previstas até hoje foram respondidas.</p>
            <div style="margin-top: 12px;">
                <div class="progress">
                    <div class="bar" style="width: {{ min(100, $engajamento['percentual_previsto']) }}%; background: var(--primary);"></div>
                </div>
                <div class="grid grid-2" style="margin-top: 12px;">
                    <div class="card" style="padding: 12px; background: var(--primary-light); border: none; box-shadow: none;">
                        <p class="tag">Total de interações</p>
                        <p style="font-size: 22px; font-weight: 700; color: var(--primary);">{{ $engajamento['total_interacoes'] }}</p>
                    </div>
                    <div class="card" style="padding: 12px; background: rgba(16, 185, 129, 0.08); border: none; box-shadow: none;">
                        <p class="tag" style="color: #047857;">Respondidas</p>
                        <p style="font-size: 22px; font-weight: 700; color: #047857;">{{ $engajamento['interacoes_concluidas'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="flex items-center justify-between" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                <h2>Andamento do tratamento</h2>
                <span class="pill" style="background: rgba(251, 191, 36, 0.16); color: #92400e;">{{ number_format($andamento['percentual_passado'], 2, ',', '.') }}% concluído</span>
            </div>
            <p class="muted">
                @if ($andamento['dias_totais'] > 0)
                    {{ $andamento['dias_passados'] }} de {{ $andamento['dias_totais'] }} dias previstos já se passaram.
                @else
                    Ainda não há informações suficientes para calcular o andamento do tratamento.
                @endif
            </p>
            <div style="margin-top: 12px;">
                <div class="progress">
                    <div class="bar" style="width: {{ min(100, $andamento['percentual_passado']) }}%; background: var(--amber);"></div>
                </div>
                <div class="grid grid-2" style="margin-top: 12px;">
                    <div class="card" style="padding: 12px; background: #f8fafc; border: none; box-shadow: none;">
                        <p class="tag">Dias previstos</p>
                        <p style="font-size: 22px; font-weight: 700; color: #0f172a;">{{ $andamento['dias_totais'] }}</p>
                    </div>
                    <div class="card" style="padding: 12px; background: #f1f5f9; border: none; box-shadow: none;">
                        <p class="tag">Previsão de término</p>
                        <p style="font-size: 18px; font-weight: 600; color: #1f2937;">{{ $andamento['fim'] ? $andamento['fim']->format('d/m/Y') : 'Não estimado' }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if ($paciente->ultima_observacao)
        <section class="card" style="margin-top: 18px;">
            <h2>Observações do médico</h2>
            <div class="observation">{{ $paciente->ultima_observacao }}</div>
        </section>
    @endif

    <section class="card" style="margin-top: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2>Histórico das metas</h2>
                <p class="muted">Visualize o preenchimento diário e a evolução de cada meta acompanhada.</p>
            </div>
        </div>

        @if ($metaCharts->isEmpty())
            <p class="muted" style="padding: 12px 0;">Ainda não há respostas registradas para as metas deste paciente.</p>
        @else
            <div class="grid" style="margin-top: 12px; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
                @foreach ($metaCharts as $metaChart)
                    <div class="card chart-card" style="box-shadow: none; border-color: #f0f4f8;">
                        <div style="display: flex; justify-content: space-between; gap: 8px; align-items: center;">
                            <div>
                                <h3 style="margin: 0; font-size: 16px;">{{ $metaChart['nome'] }}</h3>
                                <p class="tag" style="text-transform: uppercase; letter-spacing: .05em;">{{ $metaChart['tipo'] }}</p>
                            </div>
                            @if ($metaChart['legend'])
                                <span class="pill">Com legenda</span>
                            @endif
                        </div>
                        <div style="margin-top: 12px; height: 220px;">
                            <canvas id="report-chart-{{ $metaChart['meta_id'] }}"></canvas>
                        </div>
                        @if (! empty($metaChart['legend']))
                            <div style="margin-top: 10px; display: grid; gap: 8px;">
                                @foreach ($metaChart['legend'] as $legend)
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 999px; background: {{ $legend['color'] }};"></span>
                                        <div>
                                            <p style="margin: 0; font-weight: 600; font-size: 13px;">{{ $legend['label'] }}</p>
                                            @if (! empty($legend['description']))
                                                <p class="muted" style="margin: 0;">{{ $legend['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="card" style="margin-top: 18px;">
        <h2>Acompanhe em tempo real</h2>
        <p class="muted">Acesse o painel completo para visualizar gráficos e detalhes do seu acompanhamento.</p>
        <p style="margin-top: 10px;">
            <a href="{{ request()->fullUrl() }}" style="display: inline-flex; padding: 10px 16px; background: var(--primary); color: #fff; border-radius: 10px; text-decoration: none;">Abrir painel compartilhado</a>
        </p>
    </section>

    <p class="footer">Relatório gerado em {{ now()->format('d/m/Y \à\s H:i') }}.</p>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@include('partials.meta_charts_script', ['metaCharts' => $metaCharts, 'chartPrefix' => 'report-chart'])
@stack('scripts')
</body>
</html>
