@extends('layouts.app')

@section('title', 'Saúde Guardiã — Dashboard Premium')

@section('main')
    @include('partials.header', ['title' => 'Painel de Monitoramento'])

    <section class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#4b3f36] mb-2">Pacientes Ativos</h3>
            <div class="flex justify-between items-center">
                <span class="text-4xl font-bold text-[#2d3a4d]">150</span>
                <span class="text-green-600 text-sm">▲ 10%</span>
            </div>
        </div>
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#4b3f36] mb-2">Engajamento Médio</h3>
            <div class="flex justify-between items-center">
                <span class="text-4xl font-bold text-[#2d3a4d]">85%</span>
                <span class="text-green-600 text-sm">▲ 5%</span>
            </div>
        </div>
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#4b3f36] mb-2">Alertas da Semana</h3>
            <div class="flex justify-between items-center">
                <span class="text-4xl font-bold text-[#2d3a4d]">25</span>
                <span class="text-red-600 text-sm">▼ 2%</span>
            </div>
        </div>
    </section>

    <section class="card p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-[#4b3f36]">Pacientes em Alerta</h3>
            <div class="flex items-center gap-2 text-sm">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-[#fde8e8] text-[#9b1c1c] font-semibold">Crítico: 3</span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-[#fff6d8] text-[#8a6c00] font-semibold">Atenção: 5</span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="flex items-start gap-3 p-4 rounded-lg border border-[#f5d0cb] bg-[#fff7f5]">
                <img src="{{ asset('img/patient3.jpg') }}" class="w-10 h-10 rounded-full object-cover" alt="Carlos Souza">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-[#2d3a4d]">Carlos Souza</p>
                            <p class="text-sm text-[#6b5b51]">Sem registro de medicação há 3 dias</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-[#fde8e8] text-[#9b1c1c]">Crítico</span>
                    </div>
                    <p class="text-xs text-[#6b5b51] mt-1">Último evento: 06/10/2025</p>
                    <div class="mt-3 flex gap-2">
                        <a href="#" class="px-3 py-1.5 rounded-md text-sm bg-[#dceaf7] text-[#2d3a4d] hover:opacity-90">Ver detalhes</a>
                        <button class="px-3 py-1.5 rounded-md text-sm bg-[#9fc5e8] text-[#1b2432] hover:opacity-90">Contatar</button>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 p-4 rounded-lg border border-[#f2e3b3] bg-[#fffbea]">
                <img src="{{ asset('img/patient2.jpg') }}" class="w-10 h-10 rounded-full object-cover" alt="Maria Oliveira">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-[#2d3a4d]">Maria Oliveira</p>
                            <p class="text-sm text-[#6b5b51]">Perdeu sessão de exercícios ontem</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-[#fff6d8] text-[#8a6c00]">Atenção</span>
                    </div>
                    <p class="text-xs text-[#6b5b51] mt-1">Engajamento: 70%</p>
                    <div class="mt-3 flex gap-2">
                        <a href="#" class="px-3 py-1.5 rounded-md text-sm bg-[#dceaf7] text-[#2d3a4d] hover:opacity-90">Ver detalhes</a>
                        <button class="px-3 py-1.5 rounded-md text-sm bg-[#9fc5e8] text-[#1b2432] hover:opacity-90">Contatar</button>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 p-4 rounded-lg border border-[#f2e3b3] bg-[#fffbea]">
                <img src="{{ asset('img/patient4.jpg') }}" class="w-10 h-10 rounded-full object-cover" alt="Ana Pereira">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-[#2d3a4d]">Ana Pereira</p>
                            <p class="text-sm text-[#6b5b51]">Glicemia acima do alvo</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-[#fff6d8] text-[#8a6c00]">Atenção</span>
                    </div>
                    <p class="text-xs text-[#6b5b51] mt-1">Última medição: 142 mg/dL</p>
                    <div class="mt-3 flex gap-2">
                        <a href="#" class="px-3 py-1.5 rounded-md text-sm bg-[#dceaf7] text-[#2d3a4d] hover:opacity-90">Ver detalhes</a>
                        <button class="px-3 py-1.5 rounded-md text-sm bg-[#9fc5e8] text-[#1b2432] hover:opacity-90">Contatar</button>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 p-4 rounded-lg border border-[#f5d0cb] bg-[#fff7f5]">
                <img src="{{ asset('img/patient5.jpg') }}" class="w-10 h-10 rounded-full object-cover" alt="Rafael Lima">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-[#2d3a4d]">Rafael Lima</p>
                            <p class="text-sm text-[#6b5b51]">Pressão 150/100 nas últimas 24h</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-[#fde8e8] text-[#9b1c1c]">Crítico</span>
                    </div>
                    <p class="text-xs text-[#6b5b51] mt-1">Recomendado: reavaliação</p>
                    <div class="mt-3 flex gap-2">
                        <a href="#" class="px-3 py-1.5 rounded-md text-sm bg-[#dceaf7] text-[#2d3a4d] hover:opacity-90">Ver detalhes</a>
                        <button class="px-3 py-1.5 rounded-md text-sm bg-[#9fc5e8] text-[#1b2432] hover:opacity-90">Contatar</button>
                    </div>
                </div>
            </div>
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

    <section class="card p-6">
        <h3 class="text-lg font-semibold text-[#4b3f36] mb-4">Alertas Recentes</h3>
        <div class="space-y-3">
            <div class="p-4 rounded-md bg-[#fff7f5] border border-[#f5d0cb] text-[#4b3f36]">
                ⚠️ Carlos Souza não registra medicação há 3 dias.
            </div>
            <div class="p-4 rounded-md bg-[#fffbea] border border-[#f2e3b3] text-[#4b3f36]">
                ⚠️ Maria Oliveira perdeu sessão de exercícios ontem.
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const evolucaoCtx = document.getElementById('evolucaoChart').getContext('2d');
        new Chart(evolucaoCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Pacientes Ativos',
                    data: [120, 135, 128, 142, 150, 160, 155],
                    borderColor: '#9fc5e8',
                    backgroundColor: 'rgba(159, 197, 232, 0.3)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#9fc5e8'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Em dia', 'Atenção', 'Crítico'],
                datasets: [{
                    data: [85, 40, 25],
                    backgroundColor: ['#b6d7a8', '#ffe599', '#f4cccc'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    </script>
@endpush
