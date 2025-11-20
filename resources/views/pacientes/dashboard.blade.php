@extends('layouts.app')

@section('title', 'Dashboard do paciente — Saúde Guardiã')

@section('main')
    @php($metaTipos = \App\Models\Meta::TIPOS)

    <div
        id="dashboard-export-content"
        class="space-y-10"
        data-patient-slug="{{ \Illuminate\Support\Str::slug($paciente->nome ?? 'paciente') }}"
    >
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <a href="{{ route('pacientes.index') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Voltar para a listagem</a>
                <h1 class="mt-2 text-3xl font-bold text-gray-800">Dashboard do paciente</h1>
                <p class="text-gray-500">Acompanhe a evolução das metas e o andamento do tratamento de {{ $paciente->nome }}.</p>
            </div>
            <div class="flex w-full flex-col items-end gap-4 text-right lg:w-auto">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Plano</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $paciente->plano ?? '—' }}</p>
                    @if ($paciente->data_inicio)
                        <p class="text-sm text-gray-500">Desde {{ $paciente->data_inicio->format('d/m/Y') }}</p>
                    @endif
                </div>
                @php($canSend = (bool) ($paciente->whatsapp_numero || $paciente->telefone))
                <div class="flex flex-wrap justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-md border border-indigo-200 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
                        data-open-consideracoes
                        data-target-form="exportar-relatorio-form"
                    >
                        Exportar PDF
                    </button>
                    <button
                        type="button"
                        class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                        data-open-consideracoes
                        data-target-form="enviar-relatorio-form"
                        @unless($canSend) disabled title="Cadastre um número de WhatsApp para enviar o acompanhamento." @endunless
                    >
                        Enviar acompanhamento para o cliente
                    </button>
                </div>
            </div>
        </div>

        <form
            id="exportar-relatorio-form"
            action="{{ route('pacientes.dashboard.exportar-pdf', $paciente) }}"
            method="POST"
            class="hidden"
            data-export-client-side="true"
            data-pdf-filename="relatorio-{{ \Illuminate\Support\Str::slug($paciente->nome ?? 'paciente') }}-{{ now()->format('YmdHis') }}.pdf"
        >
            @csrf
            <input type="hidden" name="consideracoes" value="">
        </form>

        <form id="enviar-relatorio-form" action="{{ route('pacientes.enviar-acompanhamento', $paciente) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="consideracoes" value="">
        </form>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#2d3a4d]">Engajamento nas metas</h2>
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">
                        {{ number_format($engajamento['percentual_previsto'], 2, ',', '.') }}%
                    </span>
                </div>
                <p class="mt-3 text-sm text-gray-600">
                    {{ $engajamento['interacoes_concluidas'] }} de {{ max(1, $engajamento['previstas_ate_hoje']) }} metas previstas até hoje foram respondidas.
                </p>
                <div class="mt-6">
                    <div class="mb-2 flex items-center justify-between text-sm text-gray-500">
                        <span>Progresso até hoje</span>
                        <span>{{ number_format($engajamento['percentual_previsto'], 2, ',', '.') }}%</span>
                    </div>
                    <div class="h-3 w-full rounded-full bg-slate-200">
                        <div
                            class="h-3 rounded-full bg-indigo-500"
                            style="width: {{ min(100, $engajamento['percentual_previsto']) }}%;"
                        ></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg bg-indigo-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-indigo-600">Total de interações</p>
                            <p class="text-xl font-semibold text-indigo-800">{{ $engajamento['total_interacoes'] }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-green-600">Respondidas</p>
                            <p class="text-xl font-semibold text-green-700">{{ $engajamento['interacoes_concluidas'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#2d3a4d]">Andamento do tratamento</h2>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700">
                        {{ number_format($andamento['percentual_passado'], 2, ',', '.') }}% concluído
                    </span>
                </div>
                <p class="mt-3 text-sm text-gray-600">
                    @if ($andamento['dias_totais'] > 0)
                        {{ $andamento['dias_passados'] }} de {{ $andamento['dias_totais'] }} dias previstos já se passaram.
                    @else
                        Ainda não há informações suficientes para calcular o andamento do tratamento.
                    @endif
                </p>
                <div class="mt-6 space-y-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm text-gray-500">
                            <span>Tempo percorrido</span>
                            <span>{{ number_format($andamento['percentual_passado'], 2, ',', '.') }}%</span>
                        </div>
                        <div class="h-3 w-full rounded-full bg-slate-200">
                            <div
                                class="h-3 rounded-full bg-amber-500"
                                style="width: {{ min(100, $andamento['percentual_passado']) }}%;"
                            ></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Início</p>
                            <p class="text-lg font-semibold text-slate-700">
                                {{ $andamento['inicio'] ? $andamento['inicio']->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Previsão de término</p>
                            <p class="text-lg font-semibold text-slate-700">
                                {{ $andamento['fim'] ? $andamento['fim']->format('d/m/Y') : '—' }}
                            </p>
                        </div>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">
                        <p><strong>{{ number_format($andamento['percentual_futuro'], 2, ',', '.') }}%</strong> do tratamento ainda está por vir.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10">
            <div class="flex flex-col gap-2 mb-6">
                <h2 class="text-xl font-semibold text-[#2d3a4d]">Histórico das metas do paciente</h2>
                <p class="text-sm text-gray-500">Visualize o preenchimento diário e a evolução de cada meta acompanhada.</p>
            </div>

            @if ($metaCharts->isEmpty())
                <div class="card p-6">
                    <p class="text-sm text-gray-500">Ainda não há respostas registradas para as metas deste paciente.</p>
                </div>
            @else
                <div class="grid gap-6 xl:grid-cols-2">
                    @foreach ($metaCharts as $metaChart)
                        <div class="card p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-[#2d3a4d]">{{ $metaChart['nome'] }}</h3>
                                    <p class="text-xs uppercase tracking-wide text-slate-400">
                                        {{ $metaTipos[$metaChart['tipo']] ?? ucfirst($metaChart['tipo']) }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 h-64">
                                <canvas id="meta-chart-{{ $metaChart['meta_id'] }}"></canvas>
                            </div>

                            @if (! empty($metaChart['legend']))
                                <div class="mt-4 space-y-3">
                                    @foreach ($metaChart['legend'] as $legend)
                                        <div class="flex gap-3 text-sm text-slate-600">
                                            <span class="mt-1 h-3 w-3 rounded-full" style="background-color: {{ $legend['color'] }}"></span>
                                            <div>
                                                <p class="font-medium text-slate-700">{{ $legend['label'] }}</p>
                                                @if (! empty($legend['description']))
                                                    <p class="text-slate-500">{{ $legend['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if (! $metaChart['has_data'])
                                <p class="mt-4 text-sm text-gray-500">Ainda não há respostas registradas para esta meta.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-10 card p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-[#2d3a4d]">Próximas metas do paciente</h2>
                    <p class="text-sm text-gray-500">Envios programados que ainda não foram respondidos.</p>
                </div>
            </div>

            @forelse ($metasFuturas as $metaMessage)
                <div class="flex flex-col gap-4 border-b border-[#f3ede1] py-4 last:border-b-0 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ $metaMessage->meta->nome }}</p>
                        <p class="text-sm text-gray-500">Agendada para {{ $metaMessage->data_envio->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600">{{ str_replace('_', ' ', $metaMessage->status) }}</span>
                        <button
                            type="button"
                            class="copy-link inline-flex items-center rounded-md border border-indigo-200 px-3 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
                            data-link="{{ $metaMessage->link }}"
                        >
                            Copiar link
                        </button>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500">Não há metas futuras agendadas para este paciente.</p>
            @endforelse
        </div>
    </div>
    @include('partials.consideracoes_modal')
@endsection

@include('partials.meta_charts_script', ['metaCharts' => $metaCharts, 'chartPrefix' => 'meta-chart'])

@push('scripts')
    <script>
        const html2PdfUrl = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        let html2PdfPromise;

        function loadHtml2Pdf() {
            if (typeof html2pdf !== 'undefined') {
                return Promise.resolve(html2pdf);
            }

            if (! html2PdfPromise) {
                html2PdfPromise = new Promise((resolve, reject) => {
                    const existingScript = document.querySelector('script[data-html2pdf]');

                    if (existingScript) {
                        existingScript.addEventListener('load', () => resolve(html2pdf));
                        existingScript.addEventListener('error', () => reject(new Error('Erro ao carregar html2pdf')));
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = html2PdfUrl;
                    script.defer = true;
                    script.dataset.html2pdf = 'true';

                    script.onload = () => resolve(html2pdf);
                    script.onerror = () => reject(new Error('Erro ao carregar html2pdf'));

                    document.head.appendChild(script);
                });
            }

            return html2PdfPromise;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.copy-link');

            buttons.forEach((button) => {
                button.addEventListener('click', async () => {
                    const link = button.getAttribute('data-link');

                    try {
                        await navigator.clipboard.writeText(link);
                        const originalText = button.textContent;
                        button.textContent = 'Link copiado!';
                        button.classList.add('bg-indigo-500', 'text-white');

                        setTimeout(() => {
                            button.textContent = originalText;
                            button.classList.remove('bg-indigo-500', 'text-white');
                        }, 2000);
                    } catch (error) {
                        console.error('Não foi possível copiar o link', error);
                        alert('Não foi possível copiar o link. Tente novamente.');
                    }
                });
            });
        });

        document.addEventListener('consideracoes:export', async (event) => {
            const { form, text } = event.detail ?? {};
            const exportForm = document.getElementById('exportar-relatorio-form');

            if (! form || form !== exportForm) {
                return;
            }

            try {
                await loadHtml2Pdf();
            } catch (error) {
                console.error(error);
                alert('Não foi possível carregar o exportador de PDF. Verifique sua conexão e tente novamente.');
                return;
            }

            const exportContent = document.getElementById('dashboard-export-content');

            if (! exportContent) {
                return;
            }

            const clone = exportContent.cloneNode(true);

            if (text) {
                const noteCard = document.createElement('div');
                noteCard.className = 'card p-6 bg-indigo-50 border border-indigo-100 text-indigo-900';
                noteCard.innerHTML = `
                    <h3 class="text-lg font-semibold text-indigo-900">Considerações do médico</h3>
                    <p class="mt-2 whitespace-pre-line">${text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
                `;

                clone.insertBefore(noteCard, clone.children[1] ?? clone.firstChild);
            }

            const hiddenWrapper = document.createElement('div');
            hiddenWrapper.style.position = 'fixed';
            hiddenWrapper.style.left = '-9999px';
            hiddenWrapper.style.top = '0';
            hiddenWrapper.appendChild(clone);
            document.body.appendChild(hiddenWrapper);

            const fileName = exportForm.dataset.pdfFilename || 'relatorio-paciente.pdf';

            await html2pdf()
                .set({
                    margin: 0.5,
                    filename: fileName,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' },
                })
                .from(clone)
                .save();

            hiddenWrapper.remove();
        });
    </script>
@endpush
