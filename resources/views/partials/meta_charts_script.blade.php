@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const metaChartsData = @json($metaCharts ?? []);
            if (!Array.isArray(metaChartsData) || metaChartsData.length === 0) {
                return;
            }

            const chartPrefix = @json($chartPrefix ?? 'meta-chart');

            const bloodPressureScalePlugin = {
                id: 'bloodPressureScale',
                beforeDraw(chart, _args, opts) {
                    const zones = Array.isArray(opts?.zones) ? opts.zones : [];
                    if (zones.length === 0) {
                        return;
                    }

                    const xScale = chart.scales?.x;
                    const yScale = chart.scales?.y;

                    if (!xScale || !yScale) {
                        return;
                    }

                    const resolveRange = (scale, range) => {
                        const min = typeof range?.min === 'number' ? range.min : scale.min ?? scale.options?.min;
                        const max = typeof range?.max === 'number' ? range.max : scale.max ?? scale.options?.max;
                        return [min, max];
                    };

                    zones.forEach((zone) => {
                        const [xMin, xMax] = resolveRange(xScale, zone.x ?? null);
                        const [yMin, yMax] = resolveRange(yScale, zone.y ?? null);

                        if ([xMin, xMax, yMin, yMax].some((value) => typeof value !== 'number')) {
                            return;
                        }

                        const left = xScale.getPixelForValue(xMin);
                        const right = xScale.getPixelForValue(xMax);
                        const bottom = yScale.getPixelForValue(yMin);
                        const top = yScale.getPixelForValue(yMax);

                        const width = Math.max(0, right - left);
                        const height = Math.max(0, bottom - top);

                        if (width === 0 || height === 0) {
                            return;
                        }

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
                const padAxis = axis.pad || {};
                const pasAxis = axis.pas || {};
                const fallbackMin = typeof axis.min === 'number' ? axis.min : 50;
                const fallbackMax = typeof axis.max === 'number' ? axis.max : 220;

                const resolveAxisValue = (value, fallback) =>
                    typeof value === 'number' ? value : fallback;

                const dataset = {
                    label: chartData.datasetLabel || 'Medições (PAS x PAD)',
                    data: points.map((point) => {
                        const padValue =
                            typeof point.pad === 'number'
                                ? point.pad
                                : typeof point.x === 'number'
                                    ? point.x
                                    : null;
                        const pasValue =
                            typeof point.pas === 'number'
                                ? point.pas
                                : typeof point.y === 'number'
                                    ? point.y
                                    : null;

                        return {
                            x: typeof padValue === 'number' ? Number(padValue) : null,
                            y: typeof pasValue === 'number' ? Number(pasValue) : null,
                        };
                    }),
                    showLine: false,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    spanGaps: false,
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
                                title: (items) => {
                                    const index = items && items.length > 0 ? items[0].dataIndex : 0;
                                    return points[index]?.fullLabel || '';
                                },
                                label: (context) => {
                                    const index = context.dataIndex ?? 0;
                                    const point = points[index] || {};
                                    const value = point.valueLabel || '';
                                    const category = point.category ? ` (${point.category})` : '';
                                    return `${value}${category}`.trim();
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'PAD (mmHg)' },
                            min: resolveAxisValue(padAxis.min, resolveAxisValue(axis.padMin, fallbackMin)),
                            max: resolveAxisValue(padAxis.max, resolveAxisValue(axis.padMax, 120)),
                            ticks: { stepSize: 5 },
                            grid: { drawBorder: false },
                        },
                        y: {
                            title: { display: true, text: 'PAS (mmHg)' },
                            min: resolveAxisValue(pasAxis.min, resolveAxisValue(axis.pasMin, fallbackMin)),
                            max: resolveAxisValue(pasAxis.max, resolveAxisValue(axis.pasMax, fallbackMax)),
                            ticks: { stepSize: 10 },
                            grid: { drawBorder: false },
                        },
                    },
                };

                if (scaleZones.length > 0) {
                    options.plugins.bloodPressureScale = { zones: scaleZones };
                }

                const chartConfig = {
                    type: 'scatter',
                    data: {
                        labels: points.map((point) => point.label || ''),
                        datasets: [dataset],
                    },
                    options,
                };

                if (scaleZones.length > 0) {
                    chartConfig.plugins = [bloodPressureScalePlugin];
                }

                return new Chart(ctx, chartConfig);
            };

            metaChartsData.forEach((meta) => {
                if (!meta || !meta.meta_id || !meta.chart) {
                    return;
                }

                const canvasId = `${chartPrefix}-${meta.meta_id}`;
                const canvas = document.getElementById(canvasId);

                if (!canvas || canvas.dataset.chartInitialized) {
                    return;
                }

                const ctx = canvas.getContext('2d');
                const chartData = meta.chart;
                const chartType = chartData.type || 'bar';

                if (chartType === 'blood_pressure') {
                    renderBloodPressureChart(ctx, chartData);
                    canvas.dataset.chartInitialized = 'true';
                    return;
                }

                const isBarChart = chartType === 'bar';
                const dataset = {
                    label: chartData.datasetLabel || '',
                    data: chartData.values || [],
                };

                if (isBarChart) {
                    dataset.backgroundColor = chartData.colors || '#6366f1';
                    dataset.borderRadius = 12;
                    dataset.maxBarThickness = 28;
                } else {
                    dataset.borderColor = '#4f46e5';
                    dataset.backgroundColor = 'rgba(99, 102, 241, 0.15)';
                    dataset.fill = false;
                    dataset.tension = 0.35;
                    dataset.pointRadius = 4;
                    dataset.pointBackgroundColor = '#4f46e5';
                    dataset.pointHoverRadius = 6;
                    dataset.spanGaps = true;
                }

                const options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {}
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0,
                            },
                            grid: {
                                display: false,
                            },
                        },
                    },
                };

                const fullLabels = chartData.fullLabels || chartData.labels || [];
                options.plugins.tooltip.callbacks.title = (items) => {
                    const index = items && items.length > 0 ? items[0].dataIndex : 0;
                    return fullLabels[index] || '';
                };

                if (isBarChart) {
                    options.scales.y = {
                        beginAtZero: true,
                        min: 0,
                        max: 1,
                        ticks: {
                            stepSize: 1,
                            callback: (value) => (value === 1 ? 'Preencheu' : 'Não preencheu'),
                        },
                        grid: {
                            drawBorder: false,
                        },
                    };

                    options.plugins.tooltip.callbacks.label = (context) => {
                        return context.parsed.y === 1 ? 'Preencheu' : 'Não preencheu';
                    };
                } else {
                    options.scales.y = {
                        beginAtZero: false,
                        grid: {
                            drawBorder: false,
                        },
                    };

                    options.plugins.tooltip.callbacks.label = (context) => {
                        const value = context.parsed.y;
                        const label = dataset.label || 'Valor';
                        return typeof value === 'number' ? `${label}: ${value}` : 'Sem resposta';
                    };
                }

                canvas.dataset.chartInitialized = 'true';
                new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: chartData.labels || [],
                        datasets: [dataset],
                    },
                    options,
                });
            });
        });
    </script>
@endpush
