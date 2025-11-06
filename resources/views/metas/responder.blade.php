@extends('layouts.public')

@section('title', 'Meta de ' . $meta->nome)

@section('content')
    <div class="bg-white/90 backdrop-blur shadow-xl rounded-2xl p-8 space-y-6">
        <div class="space-y-1">
            <p class="text-sm uppercase tracking-wide text-indigo-500 font-semibold">Saúde Guardiã</p>
            <h1 class="text-3xl font-bold text-slate-800">Olá, {{ $paciente->nome }}!</h1>
            <p class="text-slate-600">Conte pra gente como você está evoluindo na meta <span class="font-semibold">{{ $meta->nome }}</span>.</p>
        </div>

        @if ($showChart)
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6">
                <h2 class="text-xl font-semibold text-indigo-700 mb-4">Obrigado por compartilhar!</h2>
                <p class="text-slate-600 mb-6">Veja como está seu engajamento até agora.</p>
                <div class="w-full">
                    <canvas id="chart-engajamento"></canvas>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-indigo-100">
                        <p class="text-sm text-slate-500">Total de interações previstas</p>
                        <p class="text-2xl font-semibold text-slate-800">{{ $engajamento['total_interacoes'] }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-indigo-100">
                        <p class="text-sm text-slate-500">Interações concluídas</p>
                        <p class="text-2xl font-semibold text-slate-800">{{ $engajamento['interacoes_concluidas'] }}</p>
                    </div>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('metas.responder.store', $metaMessage->token) }}" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="valor" class="block text-sm font-medium text-slate-700">
                        {{ filled($meta->descricao) ? $meta->descricao : 'Como você está indo com esta meta?' }}
                    </label>
                    @include('metas.responder_campos', ['meta' => $meta, 'valorAnterior' => old('valor')])
                    @error('valor')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-200 transition">
                    Enviar resposta
                </button>
            </form>
        @endif

        <p class="text-xs text-slate-400 text-center">Este link é exclusivo e seguro. Em caso de dúvidas, fale com sua equipe Saúde Guardiã.</p>
    </div>
@endsection

@push('scripts')
    @if ($showChart)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('chart-engajamento');
                if (!ctx) {
                    return;
                }

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
                            legend: {
                                display: false
                            },
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
