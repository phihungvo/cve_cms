@extends('layouts.in')

@section('body')
    <!-- Thêm Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace(); // Khởi tạo Feather Icons
        });
    </script>

    <div class="container mx-auto px-4 py-8">
        <!-- Hàng 1: Bộ lọc Enterprise và Card Enterprise -->
        @if ($auth->isRoleRoot())
            <div class="flex items-center justify-center gap-12 mb-16">
                <!-- Ô chọn Enterprise -->
                <form method="GET" class="flex justify-center">
                    <div class="w-full max-w-md">
                        <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                            placeholder="{{ __('dashboard-index.all_enterprises') }}" data-change-submit></x-select>
                    </div>
                </form>

                <!-- Card Enterprise -->
                <a href="{{ route('user.enterprise.index') }}"
                    class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                    style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                    <div class="flex flex-row items-center space-x-4">
                        <i data-feather="briefcase" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                        <div class="flex flex-col items-start">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.enterprises') }}
                            </h2>
                            <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['enterprises'] }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Hàng 2: 4 Cards (Devices, Users, Campaigns, Media) -->
        <div class="flex justify-center gap-12 mb-16">
            <!-- Card Device -->
            <a href="{{ route('device.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <div class="flex flex-row items-center space-x-4">
                    <i data-feather="monitor" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                    <div class="flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.devices') }}</h2>
                        <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['devices'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Card User -->
            <a href="{{ route('user.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <div class="flex flex-row items-center space-x-4">
                    <i data-feather="users" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                    <div class="flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.users') }}</h2>
                        <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['users'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Card Campaign -->
            <a href="{{ route('campaign.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <div class="flex flex-row items-center space-x-4">
                    <i data-feather="megaphone" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                    <div class="flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.campaigns') }}</h2>
                        <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['campaigns'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Card Media -->
            <a href="{{ route('fpp.media.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <div class="flex flex-row items-center space-x-4">
                    <i data-feather="image" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                    <div class="flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.media') }}</h2>
                        <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['media'] }}</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Hàng 3: 2 Cards (Playlists, Schedules) -->
        <div class="flex justify-center gap-12">
            <!-- Card Playlist -->
            <a href="{{ route('fpp.playlist.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <div class="flex flex-row items-center space-x-4">
                    <i data-feather="list" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                    <div class="flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.playlists') }}</h2>
                        <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['playlists'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Card Schedule -->
            <a href="{{ route('schedule.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <div class="flex flex-row items-center space-x-4">
                    <i data-feather="calendar" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                    <div class="flex flex-col items-start">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard-index.schedules') }}</h2>
                        <p class="font-bold text-gray-700" style="font-size: 5rem;">{{ $counts['schedules'] }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@stop
