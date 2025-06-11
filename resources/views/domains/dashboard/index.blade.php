@extends('layouts.in')

@section('body')
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();

            // Initialize single chart
            const ctx = document.getElementById('metrics-chart')?.getContext('2d');
            const chartData = @json($chart_data);
            if (ctx && chartData.labels && chartData.labels.length > 0 && chartData.datasets && chartData.datasets
                .length > 0) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets.map(dataset => ({
                            ...dataset,
                            borderWidth: 4, // Thicker lines
                            pointRadius: 6, // Larger points
                            pointHoverRadius: 8, // Larger points on hover
                            pointBackgroundColor: dataset
                                .borderColor, // Match point color to line
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Số lượng',
                                    font: {
                                        size: 18,
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Ngày',
                                    font: {
                                        size: 18,
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                titleFont: {
                                    size: 16
                                },
                                bodyFont: {
                                    size: 14
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            } else {
                console.warn('No chart data available or canvas not found.', {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                });
                if (document.getElementById('metrics-chart')) {
                    document.getElementById('metrics-chart').style.display = 'none';
                    document.getElementById('no-data-message').style.display = 'block';
                }
            }
        });
    </script>

    <div class="container mx-auto px-4 py-8 h-screen">
        <!-- Enterprise Filter -->
        @if (method_exists($auth, 'isRoot') && $auth->isRoot())
            <div class="flex justify-center mb-10">
                <form method="GET" class="w-full max-w-3xl">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                        placeholder="{{ __('dashboard-index.all_enterprises') }}" data-change-submit></x-select>
                </form>
            </div>
        @endif

        <!-- Single Chart -->
        <div class="w-full h-full">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200 h-full">
                <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">{{ __('dashboard-index.metrics_trend') }}</h2>
                <div class="w-full h-full">
                    <canvas id="metrics-chart"></canvas>
                    <p id="no-data-message" class="hidden text-center text-gray-600 text-lg mt-4">
                        {{ __('dashboard-index.no_data_available') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
