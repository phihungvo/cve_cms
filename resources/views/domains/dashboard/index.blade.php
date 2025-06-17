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
                const maxValue = Math.max(...chartData.datasets.flatMap(dataset => dataset.data));
                const chartHeight = ctx.canvas.height;
                const targetPosition = chartHeight * 0.66;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets.map(dataset => ({
                            ...dataset,
                            borderWidth: 2,
                            pointRadius: 0, // Giữ nguyên để bỏ dấu chấm
                            pointHoverRadius: 0, // Giữ nguyên để bỏ dấu chấm khi hover
                            pointBackgroundColor: dataset.borderColor,
                            fill: false, // Giữ nguyên để bỏ nền màu
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: false,
                                suggestedMin: 0,
                                suggestedMax: maxValue + (maxValue * 0.5),
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
                                    },
                                    stepSize: Math.max(10, maxValue / 10)
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
                                    },
                                    callback: function(value, index, values) {
                                        return index % 8 === 0 ? chartData.labels[value] :
                                            ''; // Giữ nhãn thưa
                                    },
                                    maxRotation: 5, // Đặt góc xoay tối đa là 0 độ
                                    minRotation: 0, // Đặt góc xoay tối thiểu là 0 độ
                                    padding: 10 // Thêm khoảng cách để tránh chồng lấn
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

            // Thêm sự kiện để reload khi form thay đổi
            const form = document.querySelector('form[method="GET"]');
            if (form) {
                form.addEventListener('change', function(e) {
                    if (e.target.name === 'start_date' || e.target.name === 'end_date' || e.target.name ===
                        'enterprise_id') {
                        form.submit();
                        console.log('Form submitted with:', new FormData(form));
                    }
                });

                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Ngăn submit mặc định để debug
                    const formData = new FormData(form);
                    console.log('Form data on submit:', Object.fromEntries(formData));
                    form.submit(); // Submit lại sau khi log
                });
            }
        });
    </script>

    <div class="container mx-auto px-4 py-6 h-screen">
        <div class="flex flex-col md:flex-row gap-2 h-full">
            <!-- Enterprise Filter and Date Filter Card -->
            @if (method_exists($auth, 'isRoot') && $auth->isRoot())
                <div class="flex-1 bg-white p-4 rounded-lg shadow">
                    <form method="GET" class="w-full flex flex-row gap-2 items-center">
                        <!-- Thay flex-col md:flex-row thành flex-row -->
                        <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                            placeholder="{{ __('dashboard-index.all_enterprises') }}" class="w-full md:w-auto"></x-select>
                        <div class="flex gap-1 items-center w-full md:w-auto">
                            <input type="date" name="start_date"
                                class="border border-gray-300 rounded-lg p-2 w-full md:w-auto"
                                value="{{ request('start_date') }}">
                            <input type="date" name="end_date"
                                class="border border-gray-300 rounded-lg p-2 w-full md:w-auto"
                                value="{{ request('end_date') }}">
                            {{-- <button type="submit"
                                class="bg-blue-600 text-white rounded-lg p-2 hover:bg-blue-700 w-full md:w-auto">
                                Filter
                            </button> --}}
                        </div>
                    </form>
                </div>
            @endif

            <!-- Single Chart Card -->
            <div class="flex-1 bg-white p-6 rounded-lg shadow h-full">
                <div class="w-full h-full mt-6">
                    <canvas id="metrics-chart"></canvas>
                    <p id="no-data-message" class="hidden text-center text-gray-600 text-lg mt-4">
                        {{ __('dashboard-index.no_data_available') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
