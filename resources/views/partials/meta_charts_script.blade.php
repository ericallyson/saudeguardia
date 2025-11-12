@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const metaChartsData = @json($metaCharts ?? []);
            if (!Array.isArray(metaChartsData) || metaChartsData.length === 0) {
                return;
            }

            const chartPrefix = @json($chartPrefix ?? 'meta-chart');

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
                const isBarChart = chartData.type === 'bar';
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
                    type: chartData.type || 'bar',
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
