@extends('layouts.in')

@section('body')
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>

    @php
        function dashboard_card($icon, $title, $count, $color, $href)
        {
            return <<<HTML
                <a href="$href"
                    class="transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 bg-white shadow-md hover:shadow-xl border border-gray-200 rounded-2xl p-6 flex flex-col items-center text-center min-h-[440px]">
                    <div class="w-16 h-16 rounded-full bg-$color-100 flex items-center justify-center mb-4">
                        <i data-feather="$icon" class="text-$color-600" style="width: 28px; height: 28px;"></i>
                    </div>
                    <p class="text-4xl font-extrabold text-gray-800 mb-2">$count</p>
                    <h2 class="text-lg font-semibold text-gray-600">$title</h2>
                </a>
            HTML;
        }
    @endphp

    <div class="container mx-auto px-4 py-8">
        <!-- Bộ lọc Enterprise -->
        @if ($auth->isRoleRoot())
            <div class="flex justify-center mb-10">
                <!-- Khoảng cách dọc 40px để khớp gap-10 -->
                <form method="GET" class="w-full max-w-md">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                        placeholder="{{ __('dashboard-index.all_enterprises') }}" data-change-submit></x-select>
                </form>
            </div>
        @endif

        <!-- Hàng trên: 4 cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10 justify-center max-w-6xl mx-auto">
            <!-- Khoảng cách ngang/dọc 40px -->
            @if ($auth->isRoleRoot())
                {!! dashboard_card(
                    'briefcase',
                    __('dashboard-index.enterprises'),
                    $counts['enterprises'],
                    'yellow',
                    route('user.enterprise.index'),
                ) !!}
            @endif

            {!! dashboard_card(
                'monitor',
                __('dashboard-index.devices'),
                $counts['devices'],
                'indigo',
                route('device.index'),
            ) !!}
            {!! dashboard_card('users', __('dashboard-index.users'), $counts['users'], 'blue', route('user.index')) !!}
            {!! dashboard_card(
                'volume-2',
                __('dashboard-index.campaigns'),
                $counts['campaigns'],
                'pink',
                route('campaign.index'),
            ) !!}
        </div>

        <!-- Hàng dưới: 3 cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 justify-center max-w-5xl mx-auto">
            {!! dashboard_card('image', __('dashboard-index.media'), $counts['media'], 'green', route('fpp.media.index')) !!}
            {!! dashboard_card(
                'list',
                __('dashboard-index.playlists'),
                $counts['playlists'],
                'purple',
                route('fpp.playlist.index'),
            ) !!}
            {!! dashboard_card(
                'calendar',
                __('dashboard-index.schedules'),
                $counts['schedules'],
                'red',
                route('schedule.index'),
            ) !!}
        </div>
    </div>
@stop
