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
            <div class="flex flex-wrap items-center justify-center gap-12 mb-16">
                <!-- Ô chọn Enterprise -->
                <form method="GET" class="flex justify-center">
                    <div class="w-full max-w-md">
                        <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                            placeholder="{{ __('dashboard.all_enterprises') }}" data-change-submit></x-select>
                    </div>
                </form>

                <!-- Card Enterprise -->
                <a href="{{ route('user.enterprise.index') }}"
                    class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                    style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.enterprises') }}</h2>
                    <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['enterprises'] }}</p>
                    <i data-feather="briefcase" class="text-gray-500" style="width: 40px; height: 40px;"></i>
                </a>
            </div>
        @endif

        <!-- Hàng 2: 3 Card (Devices, Users, Campaigns) -->
        <div class="flex flex-wrap justify-center gap-12 mb-16">
            <!-- Card Device -->
            <a href="{{ route('device.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.devices') }}</h2>
                <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['devices'] }}</p>
                <i data-feather="monitor" class="text-gray-500" style="width: 40px; height: 40px;"></i>
            </a>

            <!-- Card User -->
            <a href="{{ route('user.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.users') }}</h2>
                <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['users'] }}</p>
                <i data-feather="users" class="text-gray-500" style="width: 40px; height: 40px;"></i>
            </a>

            <!-- Card Campaign -->
            <a href="{{ route('campaign.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.campaigns') }}</h2>
                <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['campaigns'] }}</p>
                <i data-feather="megaphone" class="text-gray-500" style="width: 40px; height: 40px;"></i>
            </a>
        </div>

        <!-- Hàng 3: 3 Card (Media, Playlists, Schedules) -->
        <div class="flex flex-wrap justify-center gap-12">
            <!-- Card Media -->
            <a href="{{ route('fpp.media.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.media') }}</h2>
                <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['media'] }}</p>
                <i data-feather="image" class="text-gray-500" style="width: 40px; height: 40px;"></i>
            </a>

            <!-- Card Playlist -->
            <a href="{{ route('fpp.playlist.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.playlists') }}</h2>
                <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['playlists'] }}</p>
                <i data-feather="list" class="text-gray-500" style="width: 40px; height: 40px;"></i>
            </a>

            <!-- Card Schedule -->
            <a href="{{ route('schedule.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200"
                style="flex: 1 1 0; min-width: 220px; max-width: 300px; height: 300px;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('dashboard.schedules') }}</h2>
                <p class="font-bold text-gray-700 mb-6" style="font-size: 5rem;">{{ $counts['schedules'] }}</p>
                <i data-feather="calendar" class="text-gray-500" style="width: 40px; height: 40px;"></i>
            </a>
        </div>
    </div>
@stop
