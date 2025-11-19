@extends('layouts.app')

@section('title', 'Saúde Guardiã — Relatórios')

@section('main')
    @include('partials.header', [
        'title' => 'Relatórios',
        'subtitle' => 'Acompanhe o engajamento dos pacientes e exporte relatórios completos.',
    ])

    <section class="card p-6 mb-8">
        <form method="GET" class="grid md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-[#4b3f36] mb-1">Data inicial</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#4b3f36] mb-1">Data final</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
            </div>
            <div class="flex gap-3 md:col-span-2 justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#2d3a4d] text-white font-semibold hover:opacity-90">Aplicar filtros</button>
                <button type="submit" name="export" value="pdf"
                    class="px-4 py-2 rounded-lg bg-[#9fc5e8] text-[#1b2432] font-semibold hover:opacity-90">Exportar PDF</button>
            </div>
        </form>
    </section>

    @php
        $summaryCards = [
            [
                'label' => 'Pacientes ativos',
                'value' => number_format($report['totals']['pacientes'], 0, ',', '.'),
                'detail' => 'No período selecionado',
            ],
            [
                'label' => 'Metas previstas',
                'value' => number_format($report['totals']['previstas'], 0, ',', '.'),
                'detail' => 'Mensagens enviadas',
            ],
            [
                'label' => 'Metas realizadas',
                'value' => number_format($report['totals']['realizadas'], 0, ',', '.'),
                'detail' => 'Respostas concluídas',
            ],
            [
                'label' => 'Engajamento geral',
                'value' => number_format($report['engagement_rate'], 1, ',', '.') . '%',
                'detail' => 'Respostas x envios',
            ],
            [
                'label' => 'Engajamento médio',
                'value' => number_format($report['average_engagement'], 1, ',', '.') . '%',
                'detail' => 'Média por paciente',
            ],
            [
                'label' => 'Metas pendentes',
                'value' => number_format($report['totals']['pendentes'], 0, ',', '.'),
                'detail' => 'Ainda sem resposta',
            ],
        ];
    @endphp

    <section class="grid md:grid-cols-3 gap-6 mb-8">
        @foreach ($summaryCards as $card)
            <div class="card p-5">
                <p class="text-sm text-[#6b5b51]">{{ $card['label'] }}</p>
                <p class="text-3xl font-bold text-[#2d3a4d] mt-1">{{ $card['value'] }}</p>
                <p class="text-xs text-[#6b5b51] mt-1">{{ $card['detail'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="card p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-[#2d3a4d]">Pacientes e engajamento</h3>
                <p class="text-sm text-[#6b5b51]">Metas previstas e realizadas entre {{ \Illuminate\Support\Carbon::parse($filters['start_date'])->format('d/m/Y') }} e {{ \Illuminate\Support\Carbon::parse($filters['end_date'])->format('d/m/Y') }}.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="text-left text-xs font-semibold text-[#6b5b51] uppercase">
                        <th class="px-4 py-2">Paciente</th>
                        <th class="px-4 py-2">Metas previstas</th>
                        <th class="px-4 py-2">Metas realizadas</th>
                        <th class="px-4 py-2">Metas pendentes</th>
                        <th class="px-4 py-2">Engajamento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($report['patients'] as $paciente)
                        <tr>
                            <td class="px-4 py-3 font-medium text-[#2d3a4d]">{{ $paciente->nome }}</td>
                            <td class="px-4 py-3">{{ $paciente->metas_previstas }}</td>
                            <td class="px-4 py-3 text-green-700 font-semibold">{{ $paciente->metas_realizadas }}</td>
                            <td class="px-4 py-3 text-amber-700">{{ $paciente->metas_pendentes }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-2 bg-[#9fc5e8]" style="width: {{ min(100, $paciente->engajamento_percentual) }}%"></div>
                                    </div>
                                    <span class="font-semibold text-[#2d3a4d]">{{ number_format($paciente->engajamento_percentual, 1, ',', '.') }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-500">Nenhum paciente com dados no período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid md:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#2d3a4d] mb-3">Maior engajamento</h3>
            <ul class="space-y-2">
                @forelse ($report['top_engaged'] as $paciente)
                    <li class="flex items-center justify-between p-3 rounded-lg bg-[#f4f7fb]">
                        <div>
                            <p class="font-semibold text-[#2d3a4d]">{{ $paciente->nome }}</p>
                            <p class="text-xs text-[#6b5b51]">{{ $paciente->metas_realizadas }} de {{ max(1, $paciente->metas_previstas) }} metas concluídas</p>
                        </div>
                        <span class="text-sm font-bold text-green-700">{{ number_format($paciente->engajamento_percentual, 1, ',', '.') }}%</span>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">Sem dados de engajamento neste período.</li>
                @endforelse
            </ul>
        </div>
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-[#2d3a4d] mb-3">Necessitam atenção</h3>
            <ul class="space-y-2">
                @forelse ($report['lowest_engaged'] as $paciente)
                    <li class="flex items-center justify-between p-3 rounded-lg bg-[#fff6d8]">
                        <div>
                            <p class="font-semibold text-[#2d3a4d]">{{ $paciente->nome }}</p>
                            <p class="text-xs text-[#6b5b51]">{{ $paciente->metas_pendentes }} metas pendentes</p>
                        </div>
                        <span class="text-sm font-bold text-amber-700">{{ number_format($paciente->engajamento_percentual, 1, ',', '.') }}%</span>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">Sem pacientes pendentes no período.</li>
                @endforelse
            </ul>
        </div>
    </section>
@endsection
