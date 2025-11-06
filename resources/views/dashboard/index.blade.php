@extends('layouts.app')

@section('title', 'Saúde Guardiã — Dashboard Premium')

@section('main')
    @include('partials.header', ['title' => 'Painel de Monitoramento'])

    @php
        $statsConfig = [
            [
                'key' => 'active_patients',
                'title' => 'Pacientes Ativos',
                'is_percent' => false,
            ],
            [
                'key' => 'average_engagement',
                'title' => 'Engajamento Médio',
                'is_percent' => true,
            ],
            [
                'key' => 'alerts_week',
                'title' => 'Alertas da Semana',
                'is_percent' => false,
            ],
        ];
    @endphp

    <section class="grid md:grid-cols-3 gap-6 mb-8">
        @foreach ($statsConfig as $statConfig)
            @php
                $stat = $stats[$statConfig['key']];
                $formattedValue = $statConfig['is_percent']
                    ? number_format($stat['value'], 1, ',', '.') . '%'
                    : number_format($stat['value'], 0, ',', '.');
                $trend = $stat['trend'];
            @endphp
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-[#4b3f36] mb-2">{{ $statConfig['title'] }}</h3>
                <div class="flex justify-between items-center">
                    <span class="text-4xl font-bold text-[#2d3a4d]">{{ $formattedValue }}</span>
                    <span class="{{ $trend['color'] }} text-sm">
                        {{ $trend['symbol'] }}
                        {{ number_format($trend['percent'], 1, ',', '.') }}%
                    </span>
                </div>
            </div>
        @endforeach
    </section>

    @php
        $alertStyles = [
            'critico' => [
                'container' => 'border border-[#f5d0cb] bg-[#fff7f5]',
                'badge' => 'bg-[#fde8e8] text-[#9b1c1c]',
                'label' => 'Crítico',
            ],
            'atencao' => [
                'container' => 'border border-[#f2e3b3] bg-[#fffbea]',
                'badge' => 'bg-[#fff6d8] text-[#8a6c00]',
                'label' => 'Atenção',
            ],
        ];
    @endphp

    <section class="card p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-[#4b3f36]">Pacientes em Alerta</h3>
            <div class="flex items-center gap-2 text-sm">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-[#fde8e8] text-[#9b1c1c] font-semibold">
                    Crítico: {{ $alertSummary['critico'] }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-[#fff6d8] text-[#8a6c00] font-semibold">
                    Atenção: {{ $alertSummary['atencao'] }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            @forelse ($alertPatients as $alert)
                @php
                    $style = $alertStyles[$alert['severity']] ?? $alertStyles['atencao'];
                @endphp
                <div class="flex items-start gap-3 p-4 rounded-lg {{ $style['container'] }}">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center">
                        {{ $alert['initial'] }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-[#2d3a4d]">{{ $alert['paciente']->nome }}</p>
                                <p class="text-sm text-[#6b5b51]">{{ $alert['descricao'] }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $style['badge'] }}">{{ $style['label'] }}</span>
                        </div>
                        @if ($alert['ultimo_evento'])
                            <p class="text-xs text-[#6b5b51] mt-1">{{ $alert['ultimo_evento'] }}</p>
                        @endif
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('pacientes.dashboard', $alert['paciente']) }}" class="px-3 py-1.5 rounded-md text-sm bg-[#dceaf7] text-[#2d3a4d] hover:opacity-90">
                                Ver detalhes
                            </a>
                            @if ($alert['telefone_link'])
                                <a href="{{ $alert['telefone_link'] }}" class="px-3 py-1.5 rounded-md text-sm bg-[#9fc5e8] text-[#1b2432] hover:opacity-90">
                                    Contatar
                                </a>
                            @else
                                <span class="px-3 py-1.5 rounded-md text-sm bg-slate-200 text-slate-500 cursor-not-allowed">
                                    Sem contato
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-2 text-center text-sm text-slate-500 py-6">Nenhum paciente em alerta no momento.</p>
            @endforelse
        </div>
    </section>

    <section class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#4b3f36] mb-4">Evolução dos Pacientes</h3>
            <canvas id="evolucaoChart" height="180"></canvas>
        </div>
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#4b3f36] mb-4">Distribuição de Status</h3>
            <canvas id="statusChart" height="180"></canvas>
        </div>
    </section>

    @php
        $recentAlertStyles = [
            'critico' => 'bg-[#fff7f5] border border-[#f5d0cb] text-[#4b3f36]',
            'atencao' => 'bg-[#fffbea] border border-[#f2e3b3] text-[#4b3f36]',
        ];
    @endphp

    <section class="card p-6">
        <h3 class="text-lg font-semibold text-[#4b3f36] mb-4">Alertas Recentes</h3>
        <div class="space-y-3">
            @forelse ($recentAlerts as $recent)
                <div class="p-4 rounded-md {{ $recentAlertStyles[$recent['level']] ?? 'bg-slate-100 border border-slate-200 text-[#4b3f36]' }}">
                    {{ $recent['text'] }}
                </div>
            @empty
                <p class="text-sm text-slate-500">Nenhum alerta recente.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const evolucaoElement = document.getElementById('evolucaoChart');
            if (evolucaoElement) {
                const evolucaoCtx = evolucaoElement.getContext('2d');
                const evolucaoData = @json($charts['evolucao']);

                new Chart(evolucaoCtx, {
                    type: 'line',
                    data: {
                        labels: evolucaoData.labels,
                        datasets: [{
                            label: 'Pacientes engajados',
                            data: evolucaoData.values,
                            borderColor: '#9fc5e8',
                            backgroundColor: 'rgba(159, 197, 232, 0.3)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: '#9fc5e8'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            const statusElement = document.getElementById('statusChart');
            if (statusElement) {
                const statusCtx = statusElement.getContext('2d');
                const statusData = @json($charts['status']);

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusData.labels,
                        datasets: [{
                            data: statusData.values,
                            backgroundColor: statusData.colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>
@endpush
