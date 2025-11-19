@extends('layouts.public')

@section('title', 'Meta de ' . $meta->nome)

@section('content')
    <div class="bg-gradient-to-b from-slate-50 to-white/90 backdrop-blur shadow-2xl rounded-3xl p-6 md:p-10 space-y-8 border border-slate-100">
        {{-- Cabeçalho --}}
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 border border-indigo-100">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                <p class="text-xs uppercase tracking-[0.18em] text-indigo-600 font-semibold">
                    Saúde Guardiã
                </p>
            </div>

            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900">
                        Olá, <span class="text-indigo-600">{{ $paciente->nome }}</span>!
                    </h1>
                    <p class="text-slate-600 mt-1">
                        Conte pra gente como você está evoluindo na meta
                        <span class="font-semibold text-slate-900">{{ $meta->nome }}</span>.
                    </p>
                </div>

                <div class="mt-3 md:mt-0 inline-flex items-center gap-3 rounded-2xl bg-slate-900 text-slate-50 px-4 py-2 shadow-lg shadow-slate-900/20">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/20">
                        <svg class="h-5 w-5 text-indigo-200" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12.5L10 17.5L20 6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-[11px] uppercase tracking-[0.16em] text-slate-400">
                            Acompanhamento de meta
                        </p>
                        <p class="text-sm font-medium">
                            Seu bem-estar em primeiro lugar
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if ($showChart)
            {{-- Bloco de engajamento --}}
            <div class="bg-white/90 border border-indigo-100/80 rounded-2xl p-6 md:p-7 shadow-sm shadow-indigo-50 space-y-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                            Obrigado por compartilhar!
                        </h2>
                        <p class="text-slate-600">
                            Veja como está seu engajamento até agora nesta meta.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 border border-indigo-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        Engajamento em tempo real
                    </span>
                </div>

                <div class="w-full">
                    <canvas id="chart-engajamento"></canvas>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div class="relative overflow-hidden bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">
                                    Total de interações previstas
                                </p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">
                                    {{ $engajamento['total_interacoes'] }}
                                </p>
                            </div>
                            <div class="h-10 w-10 rounded-xl bg-slate-900 text-slate-50 flex items-center justify-center">
                                <span class="text-sm font-semibold">∞</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium text-indigo-700 uppercase tracking-wide">
                                    Interações concluídas
                                </p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">
                                    {{ $engajamento['interacoes_concluidas'] }}
                                </p>
                            </div>
                            <div class="h-10 w-10 rounded-xl bg-indigo-600 text-indigo-50 flex items-center justify-center">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 12.5L10 17.5L20 6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php($metaChartAtual = $metaCharts->first())

            @if ($metaChartAtual)
                {{-- Histórico da meta --}}
                <div class="bg-white/80 border border-slate-100 rounded-2xl p-6 md:p-7 space-y-5 shadow-sm">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h3 class="text-lg md:text-xl font-semibold text-slate-900">
                                Histórico da meta
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Acompanhe seus registros diários e visualize sua evolução.
                            </p>
                        </div>
                        <span class="inline-flex items-center self-start rounded-full bg-slate-100 px-3 py-1 text-[11px] font-medium text-slate-600">
                            Atualizado automaticamente
                        </span>
                    </div>

                    <div class="mt-1 h-64">
                        <canvas id="meta-response-chart-{{ $metaChartAtual['meta_id'] }}"></canvas>
                    </div>

                    @if (! empty($metaChartAtual['legend']))
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            @foreach ($metaChartAtual['legend'] as $legend)
                                <div class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3.5 py-3 text-sm text-slate-600">
                                    <span
                                        class="mt-1.5 h-3 w-3 rounded-full ring-2 ring-white shadow-sm"
                                        style="background-color: {{ $legend['color'] }}"
                                    ></span>
                                    <div>
                                        <p class="font-medium text-slate-800">
                                            {{ $legend['label'] }}
                                        </p>
                                        @if (! empty($legend['description']))
                                            <p class="text-slate-500">
                                                {{ $legend['description'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (! $metaChartAtual['has_data'])
                        <p class="mt-3 text-sm text-slate-500">
                            Você ainda não registrou respostas anteriores para esta meta.
                            Cada registro ajuda a equipe a cuidar ainda melhor de você.
                        </p>
                    @endif
                </div>
            @endif
        @else
            {{-- Formulário de resposta com DESTAQUE para a descrição da meta --}}
            <form method="POST" action="{{ route('metas.responder.store', $metaMessage->token) }}" class="space-y-7">
                @csrf

                {{-- Destaque da descrição da meta --}}
                <div class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 via-sky-50 to-white/90 p-4 md:p-5">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-indigo-50 shadow-lg shadow-indigo-500/30">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <path d="M6 10C6 7.79086 7.79086 6 10 6H14C16.2091 6 18 7.79086 18 10C18 11.8638 16.7175 13.422 15 13.874L13.5 14.25V15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="19" r="1" fill="currentColor" />
                            </svg>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-indigo-600">
                                Descrição da meta
                            </p>
                            <p class="text-sm md:text-base leading-relaxed text-slate-800">
                                {{ filled($meta->descricao) ? $meta->descricao : 'Como você está indo com esta meta?' }}
                            </p>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-indigo-100/60 blur-2xl opacity-70"></div>
                </div>

                {{-- Campos de resposta --}}
                <div class="space-y-2">
                    <label
                        @if ($meta->tipo !== 'blood_pressure') for="valor" @endif
                        class="block text-sm font-medium text-slate-700"
                    >
                        Seu registro para hoje
                    </label>

                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 transition">
                        @include('metas.responder_campos', [
                            'meta' => $meta,
                            'valorAnterior' => old('valor'),
                        ])
                    </div>

                    @error('valor')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    @error('valor_pas')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    @error('valor_pad')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 md:flex-row md:items-center md:justify-between">
                    <p class="text-xs text-slate-500">
                        Leva menos de <span class="font-semibold text-slate-700">1 minuto</span> e ajuda muito no seu acompanhamento.
                    </p>

                    <button
                        type="submit"
                        class="inline-flex w-full md:w-auto items-center justify-center gap-2 px-7 py-3.5
                               rounded-2xl bg-indigo-600 text-sm font-semibold text-white
                               shadow-lg shadow-indigo-400/30 hover:bg-indigo-700
                               focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                               transition-all"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Enviar resposta
                    </button>
                </div>
            </form>
        @endif

        <p class="text-xs text-slate-400 text-center mt-4">
            Este link é exclusivo e seguro. Em caso de dúvidas, fale com sua equipe Saúde Guardiã.
        </p>
    </div>
@endsection

@includeWhen($metaCharts->isNotEmpty(), 'partials.meta_charts_script', ['metaCharts' => $metaCharts, 'chartPrefix' => 'meta-response-chart'])

@push('scripts')
    @if ($showChart)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('chart-engajamento');
                if (!ctx) return;

                const data = {
                    labels: ['% do total concluído', '% do previsto até hoje'],
                    datasets: [{
                        data: [{{ $engajamento['percentual_total'] }}, {{ $engajamento['percentual_previsto'] }}],
                        backgroundColor: ['#6366f1', '#38bdf8'],
                        borderRadius: 12,
                    }]
                };

                new Chart(ctx, {
                    type: 'bar',
                    data,
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: value => value + '%'
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: context => context.parsed.y + '%'
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endpush
