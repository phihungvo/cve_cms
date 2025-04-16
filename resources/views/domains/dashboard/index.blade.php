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
                    class="transform hover:-translate-y-1 hover:scale-105 transition-all duration-300 bg-white shadow-md hover:shadow-xl border border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center text-center min-h-[380px] !min-h-[380px]" style="min-height: 380px !important;">
                    <div class="w-16 h-16 rounded-full bg-$color-100 flex items-center justify-center mb-4">
                        <i data-feather="$icon" class="text-$color-600" style="width: 48px; height: 48px;"></i>
                    </div>
                    <p class="text-8xl font-extrabold text-gray-800 mb-2" style="font-size: 6rem !important;">$count</p>
                    <h2 class="text-3xl font-semibold text-gray-600" style="font-size: 2rem !important;">$title</h2>
                </a>
            HTML;
        }
    @endphp

    <div class="container mx-auto px-4 py-8 h-full">
        <!-- Bộ lọc Enterprise -->
        @if ($auth->isRoleRoot())
            <div class="flex justify-center mb-10">
                <form method="GET" class="w-full max-w-3xl">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                        placeholder="{{ __('dashboard-index.all_enterprises') }}" data-change-submit></x-select>
                </form>
            </div>
        @endif

        <div
            class="grid grid-cols-1 md:grid-cols-2 @if ($auth->isRoleRoot()) lg:grid-cols-4 @else lg:grid-cols-3 @endif gap-10 mb-10 justify-center @if ($auth->isRoleRoot()) max-w-4xl @else max-w-3xl @endif mx-auto">

            @if ($auth->isRoleRoot())
                {!! dashboard_card(
                    'briefcase',
                    __('dashboard-index.enterprises'),
                    $counts['enterprises'],
                    'black',
                    route('user.enterprise.index'),
                ) !!}
            @endif

            {!! dashboard_card('monitor', __('dashboard-index.devices'), $counts['devices'], 'black', route('device.index')) !!}
            {!! dashboard_card('users', __('dashboard-index.users'), $counts['users'], 'black', route('user.index')) !!}
            {!! dashboard_card(
                'triangle',
                __('dashboard-index.campaigns'),
                $counts['campaigns'],
                'black',
                route('campaign.index'),
            ) !!}
        </div>

        <!-- Hàng dưới: 3 cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 justify-center max-w-3xl mx-auto">
            {!! dashboard_card('image', __('dashboard-index.media'), $counts['media'], 'black', route('fpp.media.index')) !!}
            {!! dashboard_card(
                'list',
                __('dashboard-index.playlists'),
                $counts['playlists'],
                'black',
                route('fpp.playlist.index'),
            ) !!}
            {!! dashboard_card(
                'calendar',
                __('dashboard-index.schedules'),
                $counts['schedules'],
                'black',
                route('schedule.index'),
            ) !!}
        </div>
    </div>
@stop
