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
        <!-- Hàng 1: Bộ lọc Enterprise -->
        @if ($auth->isRoleRoot())
            <div class="flex justify-center mb-8">
                <form method="GET" class="w-full max-w-md">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                        placeholder="{{ __('dashboard-index.all_enterprises') }}" data-change-submit></x-select>
                </form>
            </div>
        @endif

        <!-- Hàng 2: 4 Cards trên cùng -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Card Enterprise (chỉ hiển thị cho Root) -->
            @if ($auth->isRoleRoot())
                <a href="{{ route('user.enterprise.index') }}"
                    class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                    <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['enterprises'] }}</p>
                    <div class="flex items-center">
                        <i data-feather="briefcase" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                        <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.enterprises') }}</h2>
                    </div>
                </a>
            @endif

            <!-- Card Device -->
            <a href="{{ route('device.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['devices'] }}</p>
                <div class="flex items-center">
                    <i data-feather="monitor" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.devices') }}</h2>
                </div>
            </a>

            <!-- Card User -->
            <a href="{{ route('user.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['users'] }}</p>
                <div class="flex items-center">
                    <i data-feather="users" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.users') }}</h2>
                </div>
            </a>

            <!-- Card Campaign -->
            <a href="{{ route('campaign.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['campaigns'] }}</p>
                <div class="flex items-center">
                    <i data-feather="megaphone" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.campaigns') }}</h2>
                </div>
            </a>
        </div>

        <!-- Hàng 3: 3 Cards dưới cùng -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card Media -->
            <a href="{{ route('fpp.media.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['media'] }}</p>
                <div class="flex items-center">
                    <i data-feather="image" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.media') }}</h2>
                </div>
            </a>

            <!-- Card Playlist -->
            <a href="{{ route('fpp.playlist.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['playlists'] }}</p>
                <div class="flex items-center">
                    <i data-feather="list" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.playlists') }}</h2>
                </div>
            </a>

            <!-- Card Schedule -->
            <a href="{{ route('schedule.index') }}"
                class="card bg-white shadow-lg rounded-xl p-8 flex flex-col items-center hover:bg-gray-50 hover:shadow-xl transition-all duration-300 border border-gray-200 min-h-[250px]">
                <p class="font-bold text-gray-700 text-5xl mb-4">{{ $counts['schedules'] }}</p>
                <div class="flex items-center">
                    <i data-feather="calendar" class="text-gray-500 mr-2" style="width: 24px; height: 24px;"></i>
                    <h2 class="text-lg font-semibold text-gray-800">{{ __('dashboard-index.schedules') }}</h2>
                </div>
            </a>
        </div>
    </div>
@stop
