@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const metaChartsData = @json($metaCharts ?? []);
            if (!Array.isArray(metaChartsData) || metaChartsData.length === 0) {
                return;
            }

            const chartPrefix = @json($chartPrefix ?? 'meta-chart');

            const ensureCalendarStyles = () => {
                if (document.getElementById('meta-calendar-styles')) {
                    return;
                }

                const style = document.createElement('style');
                style.id = 'meta-calendar-styles';
                style.textContent = `
                    .meta-calendar { display: flex; flex-direction: column; gap: 12px; }
                    .meta-calendar-header { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
                    .meta-calendar-title { font-size: 14px; font-weight: 600; color: #334155; }
                    .meta-calendar-stats { font-size: 13px; font-weight: 600; color: #0f172a; }
                    .meta-calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
                    .meta-calendar-day-label { padding: 8px 6px; text-align: center; font-size: 11px; font-weight: 600; color: #64748b; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
                    .meta-calendar-cell { min-height: 44px; padding: 4px; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #1f2937; }
                    .meta-calendar-cell:nth-child(7n) { border-right: none; }
                    .meta-calendar-cell.empty { background: #f8fafc; }
                    .meta-calendar-cell.sim { background: #22c55e; color: #ffffff; }
                    .meta-calendar-cell.nao { background: #ef4444; color: #ffffff; }
                    .meta-calendar-cell.sem-dados { background: #e5e7eb; color: #6b7280; }
                `;

                document.head.appendChild(style);
            };

            const renderCalendarChart = (hostElement, chartData) => {
                ensureCalendarStyles();

                const entries = Array.isArray(chartData.entries) ? chartData.entries : [];
                if (entries.length === 0) {
                    hostElement.innerHTML = '<p class="text-sm text-slate-500">Sem dados para exibir no calendário.</p>';
                    return;
                }

                const byMonth = entries.reduce((acc, entry) => {
                    if (!entry?.date) {
                        return acc;
                    }

                    const date = new Date(`${entry.date}T00:00:00`);
                    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                    if (!acc[key]) {
                        acc[key] = [];
                    }
                    acc[key].push(entry);
                    return acc;
                }, {});

                const monthKeys = Object.keys(byMonth).sort();
                const monthKey = monthKeys[monthKeys.length - 1];
                const [year, month] = monthKey.split('-').map(Number);
                const monthEntries = byMonth[monthKey] || [];
                const statusByDate = monthEntries.reduce((acc, entry) => {
                    acc[entry.date] = entry.status || 'sem-dados';
                    return acc;
                }, {});

                const firstDay = new Date(year, month - 1, 1).getDay();
                const daysInMonth = new Date(year, month, 0).getDate();

                let simCount = 0;
                let withData = 0;
                monthEntries.forEach((entry) => {
                    if (entry.status === 'sim') {
                        simCount += 1;
                        withData += 1;
                    } else if (entry.status === 'nao') {
                        withData += 1;
                    }
                });

                const rate = withData > 0 ? Math.round((simCount / withData) * 100) : 0;
                const monthName = new Date(year, month - 1, 1).toLocaleDateString('pt-BR', {
                    month: 'long',
                    year: 'numeric',
                });

                const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                const header = weekDays.map((label) => `<div class="meta-calendar-day-label">${label}</div>`).join('');

                let cells = '';
                for (let i = 0; i < firstDay; i += 1) {
                    cells += '<div class="meta-calendar-cell empty"></div>';
                }

                for (let day = 1; day <= daysInMonth; day += 1) {
                    const iso = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const status = statusByDate[iso] || 'sem-dados';
                    const titleStatus = status === 'sim' ? 'Meta cumprida' : (status === 'nao' ? 'Meta não cumprida' : 'Sem dados');
                    cells += `<div class="meta-calendar-cell ${status}" title="${day}/${String(month).padStart(2, '0')}/${year} - ${titleStatus}">${day}</div>`;
                }

                hostElement.innerHTML = `
                    <div class="meta-calendar">
                        <div class="meta-calendar-header">
                            <span class="meta-calendar-title">${monthName.charAt(0).toUpperCase() + monthName.slice(1)}</span>
                            <span class="meta-calendar-stats">Taxa de sucesso: ${rate}% (${simCount}/${withData})</span>
                        </div>
                        <div class="meta-calendar-grid">
                            ${header}
                            ${cells}
                        </div>
                    </div>
                `;
            };

            const bloodPressureScalePlugin = {
                id: 'bloodPressureScale',
                beforeDraw(chart, _args, opts) {
                    const zones = Array.isArray(opts?.zones) ? opts.zones : [];
                    if (zones.length === 0) return;

                    const xScale = chart.scales?.x;
                    const yScale = chart.scales?.y;
                    if (!xScale || !yScale) return;

                    const resolveRange = (scale, range) => {
                        const min = typeof range?.min === 'number' ? range.min : scale.min ?? scale.options?.min;
                        const max = typeof range?.max === 'number' ? range.max : scale.max ?? scale.options?.max;
                        return [min, max];
                    };

                    zones.forEach((zone) => {
                        const [xMin, xMax] = resolveRange(xScale, zone.x ?? null);
                        const [yMin, yMax] = resolveRange(yScale, zone.y ?? null);
                        if ([xMin, xMax, yMin, yMax].some((value) => typeof value !== 'number')) return;

                        const left = xScale.getPixelForValue(xMin);
                        const right = xScale.getPixelForValue(xMax);
                        const bottom = yScale.getPixelForValue(yMin);
                        const top = yScale.getPixelForValue(yMax);
                        const width = Math.max(0, right - left);
                        const height = Math.max(0, bottom - top);
                        if (width === 0 || height === 0) return;

                        chart.ctx.save();
                        chart.ctx.fillStyle = zone.backgroundColor || 'rgba(148, 163, 184, 0.1)';
                        chart.ctx.fillRect(left, top, width, height);
                        if (zone.borderColor) {
                            chart.ctx.strokeStyle = zone.borderColor;
                            chart.ctx.lineWidth = typeof zone.borderWidth === 'number' ? zone.borderWidth : 1;
                            chart.ctx.strokeRect(left, top, width, height);
                        }
                        chart.ctx.restore();
                    });
                },
            };

            const renderBloodPressureChart = (ctx, chartData) => {
                const points = Array.isArray(chartData.points) ? chartData.points : [];
                const axis = chartData.axis || {};
                const colors = points.map((point) => point.color || '#4f46e5');
                const scaleZones = Array.isArray(chartData.scaleZones) ? chartData.scaleZones : [];

                const dataset = {
                    label: chartData.datasetLabel || 'Medições (PAS x PAD)',
                    data: points.map((point) => ({ x: Number(point.pad), y: Number(point.pas) })),
                    showLine: false,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: colors.length > 0 ? colors : '#4f46e5',
                    pointBorderColor: colors.length > 0 ? colors : '#4f46e5',
                };

                const options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => points[items?.[0]?.dataIndex || 0]?.fullLabel || '',
                                label: (context) => {
                                    const point = points[context.dataIndex ?? 0] || {};
                                    return `${point.valueLabel || ''}${point.category ? ` (${point.category})` : ''}`.trim();
                                },
                            },
                        },
                    },
                    scales: {
                        x: { title: { display: true, text: 'PAD (mmHg)' }, min: axis?.pad?.min ?? 50, max: axis?.pad?.max ?? 120 },
                        y: { title: { display: true, text: 'PAS (mmHg)' }, min: axis?.pas?.min ?? 50, max: axis?.pas?.max ?? 220 },
                    },
                };

                const config = { type: 'scatter', data: { datasets: [dataset] }, options };
                if (scaleZones.length > 0) {
                    options.plugins.bloodPressureScale = { zones: scaleZones };
                    config.plugins = [bloodPressureScalePlugin];
                }

                return new Chart(ctx, config);
            };

            metaChartsData.forEach((meta) => {
                if (!meta || !meta.meta_id || !meta.chart) return;

                const chartId = `${chartPrefix}-${meta.meta_id}`;
                const target = document.getElementById(chartId);
                if (!target || target.dataset.chartInitialized) return;

                const chartData = meta.chart;
                const chartType = chartData.type || 'bar';

                if (chartType === 'calendar') {
                    let host = target;

                    if (target instanceof HTMLCanvasElement) {
                        host = document.createElement('div');
                        host.id = target.id;
                        host.className = target.className;
                        target.replaceWith(host);
                    }

                    renderCalendarChart(host, chartData);
                    host.dataset.chartInitialized = 'true';
                    return;
                }

                if (!(target instanceof HTMLCanvasElement) || typeof Chart === 'undefined') {
                    return;
                }

                const ctx = target.getContext('2d');

                if (chartType === 'blood_pressure') {
                    renderBloodPressureChart(ctx, chartData);
                    target.dataset.chartInitialized = 'true';
                    return;
                }

                const isBarChart = chartType === 'bar';
                const dataset = {
                    label: chartData.datasetLabel || '',
                    data: chartData.values || [],
                    borderColor: '#4f46e5',
                    backgroundColor: isBarChart ? (chartData.colors || '#6366f1') : 'rgba(99, 102, 241, 0.15)',
                    fill: false,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    spanGaps: true,
                };

                const options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => (chartData.fullLabels || chartData.labels || [])[items?.[0]?.dataIndex || 0] || '',
                                label: (context) => {
                                    const value = context.parsed.y;
                                    return typeof value === 'number' ? `${dataset.label || 'Valor'}: ${value}` : 'Sem resposta';
                                },
                            },
                        },
                    },
                    scales: {
                        x: { ticks: { maxRotation: 0, minRotation: 0 }, grid: { display: false } },
                        y: { beginAtZero: false, grid: { drawBorder: false } },
                    },
                };

                new Chart(ctx, {
                    type: chartType,
                    data: { labels: chartData.labels || [], datasets: [dataset] },
                    options,
                });

                target.dataset.chartInitialized = 'true';
            });
        });
    </script>
@endpush
