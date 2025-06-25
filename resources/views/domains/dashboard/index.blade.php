@extends('layouts.in')

@section('body')
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            // Handle notification bell click
            const bell = document.getElementById('notification-bell');
            const dropdown = document.getElementById('notification-dropdown');

            if (bell && dropdown) {
                bell.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function (e) {
                    if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            } else {
                console.warn("Notification bell or dropdown not found.");
            }

            // Form event listeners
            const form = document.querySelector('form[method="GET"]');
            if (form) {
                form.addEventListener('change', function (e) {
                    if (e.target.name === 'start_date' || e.target.name === 'end_date' || e.target.name === 'enterprise_id') {
                        form.submit();
                        console.log('Form submitted with:', new FormData(form));
                    }
                });

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    console.log('Form data on submit:', Object.fromEntries(formData));
                    form.submit();
                });
            }
        });
    </script>

    <div class="container mx-auto px-4 py-6 h-screen relative">


        <div class="flex flex-col gap-2 h-full">
            <!-- Enterprise Filter and Date Filter Card -->
            <!-- Enterprise Filter and Date Filter Card -->
            @if (method_exists($auth, 'isRoot') && $auth->isRoot())
                <div class="w-full bg-white p-4 rounded-lg shadow flex flex-row items-center justify-between">
                    <form method="GET" class="flex flex-row gap-2 items-center">
                        <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                                  placeholder="{{ __('dashboard-index.all_enterprises') }}"
                                  class="w-full md:w-auto"></x-select>
                        <div class="flex gap-1 items-center w-full md:w-auto">
                            <input type="date" name="start_date"
                                   class="border border-gray-300 rounded-lg p-2 w-full md:w-auto focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-200"
                                   value="{{ request('start_date') }}">
                            <input type="date" name="end_date"
                                   class="border border-gray-300 rounded-lg p-2 w-full md:w-auto focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-200"
                                   value="{{ request('end_date') }}">
                        </div>
                    </form>

                    <!-- Notification Bell Container -->
                    <div class="relative ml-4 group">
                        <div id="notification-bell"
                             class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full
                        cursor-pointer hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-110
                        shadow-lg hover:shadow-xl ring-2 ring-blue-100 hover:ring-blue-200">
                            <i data-feather="bell" class="w-5 h-5"></i>
                            @if(count($notifications) > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center
                                 justify-center text-xs font-bold shadow-lg ring-2 ring-white animate-pulse">{{ count($notifications) }}</span>
                            @endif
                        </div>

                        <!-- Dropdown - hiển thị khi hover vào group -->
                        <div id="notification-dropdown"
                             class="absolute top-full right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100
                        hidden group-hover:block z-50 transform origin-top-right">

                            <!-- Arrow pointer -->
                            <div
                                class="absolute -top-2 right-6 w-4 h-4 bg-white transform rotate-45 border-l border-t border-gray-100"></div>

                            <!-- Header -->
                            <div
                                class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-t-2xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i data-feather="bell" class="w-4 h-4 text-blue-600"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-800">Thông báo</h3>
                                    </div>
                                    @if(count($notifications) > 0)
                                        <span
                                            class="bg-blue-500 text-white text-xs px-3 py-1 rounded-full font-medium">{{ count($notifications) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Notifications List -->
                            <div
                                class="max-h-72 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                @forelse ($notifications as $index => $notification)
                                    <div class="p-4 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 border-b border-gray-50 last:border-b-0
                                    transition-all duration-200 cursor-pointer group/item {{ $index < 3 ? '' : 'bg-gray-25' }}">
                                        <div class="flex items-start gap-4">
                                            <!-- Status dot -->
                                            <div class="flex-shrink-0 mt-2">
                                                <div
                                                    class="w-3 h-3 bg-blue-500 rounded-full ring-4 ring-blue-100 group-hover/item:bg-blue-600 group-hover/item:ring-blue-200 transition-all duration-200"></div>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <h4 class="text-sm font-semibold text-gray-900 group-hover/item:text-blue-700 transition-colors duration-200 leading-tight">
                                                        {{ $notification['title'] }}
                                                    </h4>
                                                    @if($index === 0)
                                                        <span
                                                            class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full font-medium flex-shrink-0">Mới</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-600 mt-2 leading-relaxed line-clamp-2">
                                                    {{ $notification['content'] }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-3 text-xs text-gray-400">
                                                    <i data-feather="clock" class="w-3 h-3"></i>
                                                    <span>{{ $notification['date'] ?? 'Không có ngày' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center">
                                        <div
                                            class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                            <i data-feather="bell-off" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <h4 class="text-sm font-medium text-gray-600 mb-1">Không có thông báo</h4>
                                        <p class="text-xs text-gray-400">Bạn sẽ nhận được thông báo ở đây</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Footer -->
                            @if(count($notifications) > 0)
                                <div
                                    class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-b-2xl border-t border-gray-100">
                                    <button class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-semibold
                                       py-2 px-4 rounded-lg hover:bg-blue-50 transition-all duration-200 transform hover:scale-105">
                                        Xem tất cả thông báo →
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- JavaScript chỉ để handle click events và feather icons -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Handle notification bell click - chuyển từ hover sang click
                    const bell = document.getElementById('notification-bell');
                    const dropdown = document.getElementById('notification-dropdown');

                    if (bell && dropdown) {
                        bell.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropdown.classList.toggle('hidden');
                            dropdown.classList.toggle('block');
                        });

                        document.addEventListener('click', function (e) {
                            if (!bell.parentElement.contains(e.target)) {
                                dropdown.classList.add('hidden');
                                dropdown.classList.remove('block');
                            }
                        });
                    }
                });
            </script>

            <!-- Single Chart Card -->
            <div class="w-full bg-white p-6 rounded-lg shadow h-full">
                <div class="w-full h-full mt-6">
                    <canvas id="metrics-chart"></canvas>
                    <p id="no-data-message" class="hidden text-center text-gray-600 text-lg mt-4">
                        {{ __('dashboard-index.no_data_available') }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
