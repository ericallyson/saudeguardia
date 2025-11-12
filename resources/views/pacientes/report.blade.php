<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Acompanhamento — {{ $paciente->nome }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: #f8f5f1;
            color: #2d3a4d;
        }
        header {
            background: #fff;
            padding: 24px;
            border-bottom: 1px solid #e3d7c3;
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }
        header img {
            height: 48px;
        }
        main {
            max-width: 960px;
            margin: 32px auto;
            padding: 0 24px 48px;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        h1 {
            margin: 0;
            font-size: 28px;
        }
        h2 {
            font-size: 20px;
            margin-bottom: 12px;
        }
        p { margin: 4px 0; }
        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .metric {
            background: #f0f4ff;
            padding: 16px;
            border-radius: 12px;
        }
        .upcoming { margin-top: 16px; }
        .upcoming-item {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e0e7ff;
            margin-bottom: 12px;
            background: #fff;
        }
        .footer {
            margin-top: 32px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        a.button {
            display: inline-flex;
            padding: 12px 18px;
            background: #2563eb;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 16px;
        }
        @media (max-width: 640px) {
            main { padding: 0 16px 32px; }
            header { align-items: flex-start; }
        }
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
<main>
    <section class="card">
        <h2>Engajamento nas metas</h2>
        <div class="metrics">
            <div class="metric">
                <strong>Metas previstas até hoje</strong>
                <p>{{ $engajamento['previstas_ate_hoje'] }}</p>
            </div>
            <div class="metric">
                <strong>Metas respondidas</strong>
                <p>{{ $engajamento['interacoes_concluidas'] }}</p>
            </div>
            <div class="metric">
                <strong>Percentual de respostas</strong>
                <p>{{ number_format($engajamento['percentual_previsto'], 2, ',', '.') }}%</p>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>Andamento do tratamento</h2>
        <div class="metrics">
            <div class="metric">
                <strong>Dias previstos</strong>
                <p>{{ $andamento['dias_totais'] }}</p>
            </div>
            <div class="metric">
                <strong>Dias concluídos</strong>
                <p>{{ $andamento['dias_passados'] }}</p>
            </div>
            <div class="metric">
                <strong>Previsão de término</strong>
                <p>{{ $andamento['fim'] ? $andamento['fim']->format('d/m/Y') : 'Não estimado' }}</p>
            </div>
            <div class="metric">
                <strong>Progresso estimado</strong>
                <p>{{ number_format($andamento['percentual_passado'], 2, ',', '.') }}%</p>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>Próximos acompanhamentos</h2>
        <div class="upcoming">
            @forelse ($metasFuturas as $index => $metaMessage)
                <div class="upcoming-item">
                    <strong>{{ $metaMessage->meta->nome ?? 'Meta sem nome' }}</strong>
                    <p>{{ $metaMessage->data_envio->format('d/m/Y \à\s H:i') }}</p>
                </div>
            @empty
                <p>Não há acompanhamentos futuros agendados.</p>
            @endforelse
        </div>
    </section>

    <section class="card">
        <h2>Acompanhe em tempo real</h2>
        <p>Acesse o painel completo para visualizar gráficos e detalhes do seu acompanhamento.</p>
        <a class="button" href="{{ request()->fullUrl() }}">Abrir painel compartilhado</a>
    </section>

    <p class="footer">Relatório gerado em {{ now()->format('d/m/Y \à\s H:i') }}.</p>
</main>
</body>
</html>
