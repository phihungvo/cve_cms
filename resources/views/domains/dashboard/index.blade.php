@extends('layouts.in')

@section('body')
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();

            // Initialize WebSocket for real-time updates
            const socket = new WebSocket('ws://your-websocket-url'); // Replace with actual WebSocket URL
            socket.onmessage = function(event) {
                const data = JSON.parse(event.data);
                updateDashboardCards(data.counts);
            };

            // Function to update card counts
            function updateDashboardCards(counts) {
                Object.keys(counts).forEach(key => {
                    const element = document.querySelector(`#${key}-count`);
                    if (element) {
                        element.textContent = counts[key];
                    }
                });
            }

            // Initialize single chart
            const ctx = document.getElementById('metrics-chart')?.getContext('2d');
            const chartData = @json($chart_data);
            if (ctx && chartData.labels && chartData.labels.length > 0 && chartData.datasets && chartData.datasets
                .length > 0) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Count'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
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

    @php
        function dashboard_card($icon, $title, $count, $color, $href, $id)
        {
            return <<<HTML
                <a href="$href"
                    class="transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 bg-white shadow-md hover:shadow-xl border border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center text-center min-h-[380px] !min-h-[380px]" style="min-height: 380px !important;">
                    <div class="w-16 h-16 rounded-full bg-$color-100 flex items-center justify-center mb-4">
                        <i data-feather="$icon" class="text-$color-600" style="width: 48px; height: 48px;"></i>
                    </div>
                    <p id="$id-count" class="text-8xl font-extrabold text-gray-800 mb-2" style="font-size: 6rem !important;">$count</p>
                    <h2 class="text-3xl font-semibold text-gray-600" style="font-size: 2rem !important;">$title</h2>
                </a>
            HTML;
        }
    @endphp

    <div class="container mx-auto px-4 py-8 h-full">
        <!-- Enterprise Filter -->
        @if ($auth->isRoleRoot())
            <div class="flex justify-center mb-10">
                <form method="GET" class="w-full max-w-3xl">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                        placeholder="{{ __('dashboard-index.all_enterprises') }}" data-change-submit></x-select>
                </form>
            </div>
        @endif

        <!-- Cards -->
        <div
            class="grid grid-cols-1 md:grid-cols-2 @if ($auth->isRoleRoot()) lg:grid-cols-4 @else lg:grid-cols-3 @endif gap-10 mb-10 justify-center @if ($auth->isRoleRoot()) max-w-4xl @else max-w-3xl @endif mx-auto">
            @if ($auth->isRoleRoot())
                {!! dashboard_card(
                    'briefcase',
                    __('dashboard-index.enterprises'),
                    $counts['enterprises'],
                    'black',
                    route('user.enterprise.index'),
                    'enterprises',
                ) !!}
            @endif
            {!! dashboard_card(
                'monitor',
                __('dashboard-index.devices'),
                $counts['devices'],
                'black',
                route('device.index'),
                'devices',
            ) !!}
            {!! dashboard_card(
                'users',
                __('dashboard-index.users'),
                $counts['users'],
                'black',
                route('user.index'),
                'users',
            ) !!}
            {!! dashboard_card(
                'triangle',
                __('dashboard-index.campaigns'),
                $counts['campaigns'],
                'black',
                route('campaign.index'),
                'campaigns',
            ) !!}
        </div>

        <!-- Single Chart -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-md rounded-2xl p-6">
                <h2 class="text-2xl font-semibold text-gray-600 mb-4">{{ __('dashboard-index.metrics_trend') }}</h2>
                <div class="w-full h-96">
                    <canvas id="metrics-chart"></canvas>
                    <p id="no-data-message" class="hidden text-center text-gray-600 mt-4">
                        {{ __('dashboard-index.no_data_available') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
