@extends('layouts.in')

@section('body')

    <form method="get" id="trip-form">
        <div class="lg:flex lg:space-x-4">
            <div class="flex-grow mt-2 lg:mt-0">
                <input type="search" class="form-control form-control-lg" placeholder="{{ __('trip-index.filter') }}"
                       data-table-search="#trip-list-table"/>
            </div>

            @if ($users_multiple)
                <div class="flex-grow mt-2 lg:mt-0">
                    <x-select name="user_id" :options="$users" value="id" text="name"
                              placeholder="{{ __('trip-index.user') }}"
                              data-change-submit></x-select>
                </div>
            @endif

            <div class="flex-grow mt-2 lg:mt-0">
                <x-select name="vehicle_id" :options="$vehicles" value="id" text="name"
                          placeholder="{{ __('trip-index.vehicle') }}" data-change-submit></x-select>
            </div>

            <div class="flex-grow mt-2 lg:mt-0">
                <x-select name="device_id" :options="$devices" value="id" text="name"
                          placeholder="{{ __('trip-index.device') }}" data-change-submit></x-select>
            </div>

            <div class="flex-grow mt-2 lg:mt-0">
                <input type="search" name="start_at" value="{{ $REQUEST->input('start_at') }}"
                       class="form-control form-control-lg" placeholder="{{ __('trip-index.start-at') }}"
                       data-datepicker
                       data-datepicker-min-date="{{ $date_min }}" data-change-submit/>
            </div>

            <div class="flex-grow mt-2 lg:mt-0">
                <input type="search" name="end_at" value="{{ $REQUEST->input('end_at') }}"
                       class="form-control form-control-lg" placeholder="{{ __('trip-index.end-at') }}" data-datepicker
                       data-datepicker-min-date="{{ $date_min }}" data-change-submit/>
            </div>

            <div class="flex-grow mt-2 lg:mt-0">
                <x-select name="shared" :options="$shared" data-change-submit></x-select>
            </div>

            <div class="flex-grow mt-2 lg:mt-0">
                <x-select name="shared_public" :options="$shared_public" data-change-submit></x-select>
            </div>

            <div class="lg:ml-4 mt-2 lg:mt-0 bg-white">
                <a href="{{ route('trip.heatmap') }}"
                   class="btn form-control-lg whitespace-nowrap">{{ __('trip-index.heatmap') }}</a>
            </div>

            <div class="lg:ml-4 mt-2 lg:mt-0 bg-white">
                <a href="{{ route('trip.search') }}"
                   class="btn form-control-lg whitespace-nowrap">{{ __('trip-index.search') }}</a>
            </div>

            <div class="lg:ml-4 mt-2 lg:mt-0 bg-white">
                <a href="{{ route('trip.import') }}"
                   class="btn form-control-lg whitespace-nowrap">{{ __('trip-index.import') }}</a>
            </div>

            <div class="lg:ml-4 mt-2 lg:mt-0 bg-white">
                <button type="button" class="btn form-control-lg whitespace-nowrap" onclick="openExportModal()">
                    {{ __('trip-index.export') }}
                </button>
            </div>
        </div>
    </form>

    <!-- Modal Export với Inline CSS -->
    <div id="exportModal"
         style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; width: 400px; max-width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h5 style="margin: 0; font-size: 18px;">{{ __('trip-index.select-export-type') }}</h5>
                <button onclick="closeExportModal()"
                        style="background: none; border: none; font-size: 16px; cursor: pointer;">✖
                </button>
            </div>
            <form id="exportForm" action="{{ route('trip.export.selected') }}" method="POST">
                @csrf
                <input type="hidden" name="selected_rows" id="selectedRows">

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">{{ __('trip-index.export-type') }}</label>
                    <div>
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" name="export_type[]" value="user" id="exportUser"
                                   style="margin-right: 5px;">
                            <label for="exportUser">{{ __('trip-index.by-user') }}</label>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" name="export_type[]" value="vehicle" id="exportVehicle"
                                   style="margin-right: 5px;">
                            <label for="exportVehicle">{{ __('trip-index.by-vehicle') }}</label>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" name="export_type[]" value="device" id="exportDevice"
                                   style="margin-right: 5px;">
                            <label for="exportDevice">{{ __('trip-index.by-device') }}</label>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" name="export_type[]" value="day" id="exportDay"
                                   style="margin-right: 5px;">
                            <label for="exportDay">{{ __('trip-index.by-day') }}</label>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" name="export_type[]" value="month" id="exportMonth"
                                   style="margin-right: 5px;">
                            <label for="exportMonth">{{ __('trip-index.by-month') }}</label>
                        </div>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="submit"
                            style="background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                        {{ __('trip-index.btn-export') }}
                    </button>
                    <button type="button" onclick="closeExportModal()"
                            style="background: #fff; color: #000; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                        {{ __('trip-index.btn-cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="overflow-auto scroll-visible header-sticky">
        <table id="trip-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
               data-table-sort
               data-table-pagination data-table-pagination-limit="10">
            <thead>
            <tr>
                <th>
                    <input type="checkbox" id="select-all"/>
                </th>
                @if ($user_empty)
                    <th>{{ __('trip-index.user') }}</th>
                @endif

                @if ($vehicle_empty)
                    <th>{{ __('trip-index.vehicle') }}</th>
                @endif

                @if ($device_empty)
                    <th>{{ __('trip-index.device') }}</th>
                @endif

                <th class="text-left">{{ __('trip-index.name') }}</th>
                <th>{{ __('trip-index.start_at') }}</th>
                <th>{{ __('trip-index.end_at') }}</th>
                <th>{{ __('trip-index.distance') }}</th>
                <th>{{ __('trip-index.time') }}</th>
                <th>{{ __('trip-index.shared') }}</th>
                <th>{{ __('trip-index.shared_public') }}</th>
                <th>{{ __('trip-index.actions') }}</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($list as $row)
                @php ($link = route('trip.update.map', $row->id))
                <tr>
                    <td>
                        <input type="checkbox" name="selected_rows[]" value="{{ $row->id }}" class="select-row"/>
                    </td>
                    @if ($user_empty)
                        <td><a href="{{ $link }}" class="block">{{ $row->user->name }}</a></td>
                    @endif

                    @if ($vehicle_empty)
                        <td><a href="{{ $link }}" class="block">{{ $row->vehicle->name }}</a></td>
                    @endif

                    @if ($device_empty)
                        <td><a href="{{ $link }}" class="block">{{ $row->device->name }}</a></td>
                    @endif

                    <td class="text-left"><a href="{{ $link }}" class="d-t-m-o max-w-md"
                                             title="{{ $row->name }}">{{ $row->name }}</a></td>

                    <td class="w-1" data-table-sort-value="{{ $row->start_at }}"><a href="{{ $link }}"
                                                                                    class="block">@dateLocal($row->start_at)</a>
                    </td>
                    <td class="w-1" data-table-sort-value="{{ $row->end_at }}"><a href="{{ $link }}"
                                                                                  class="block">@dateLocal($row->end_at)</a>
                    </td>

                    <td data-table-sort-value="{{ $row->distance }}"><a href="{{ $link }}"
                                                                        class="block">@unitHuman('distance', $row->distance)</a>
                    </td>
                    <td data-table-sort-value="{{ $row->time }}"><a href="{{ $link }}"
                                                                    class="block">@timeHuman($row->time)</a></td>
                    <td data-table-sort-value="{{ (int) $row->shared }}" class="w-1"><a
                            href="{{ route('trip.update.boolean', [$row->id, 'shared']) }}" class="block"
                            data-update-boolean="shared">@status($row->shared)</a></td>
                    <td data-table-sort-value="{{ (int) $row->shared_public }}" class="w-1"><a
                            href="{{ route('trip.update.boolean', [$row->id, 'shared_public']) }}" class="block"
                            data-update-boolean="shared_public">@status($row->shared_public)</a></td>

                    <td class="w-1">
                        <a href="{{ route('trip.update', $row->id) }}">@icon('edit', 'w-4 h-4')</a>
                        <span class="mx-2"></span>
                        <a href="{{ route('trip.update.stat', $row->id) }}">@icon('bar-chart-2', 'w-4 h-4')</a>
                        <span class="mx-2"></span>
                        <a href="{{ $link }}">@icon('map', 'w-4 h-4')</a>
                        <span class="mx-2"></span>
                        <a href="{{ route('trip.update.position', $row->id) }}">@icon('map-pin', 'w-4 h-4')</a>
                        <span class="mx-2"></span>
                        <a href="{{ route('trip.update.alarm-notification', $row->id) }}">@icon('bell', 'w-4 h-4')</a>
                        <span class="mx-2"></span>
                        <a href="{{ route('trip.update.merge', $row->id) }}">@icon('git-merge', 'w-4 h-4')</a>
                        <span class="mx-2"></span>
                        <a href="{{ route('trip.update.export', $row->id) }}">@icon('package', 'w-4 h-4')</a>
                    </td>
                </tr>
            @endforeach
            </tbody>

            <tfoot class="bg-white">
            <tr>
                <th colspan="{{ 4 + intval($user_empty) + intval($vehicle_empty) + intval($device_empty) }}"></th>
                <th>@unitHuman('distance', $list->sum('distance'))</th>
                <th>@timeHuman($list->sum('time'))</th>
                <th colspan="3"></th>
            </tr>
            </tfoot>
        </table>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function notifyWarning(message) {
                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    title: message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            }

            // Hàm mở modal
            function openExportModal() {

                document.getElementById('exportModal').style.display = 'block';
            }

            // Hàm đóng modal
            function closeExportModal() {

                document.getElementById('exportModal').style.display = 'none';
            }

            // Xử lý checkbox "select all"
            document.getElementById('select-all').addEventListener('change', function () {

                const checkboxes = document.querySelectorAll('.select-row');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Xử lý submit form export
            document.getElementById('exportForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Ngăn submit mặc định để kiểm tra


                const selectedRows = Array.from(document.querySelectorAll('.select-row:checked')).map(checkbox => checkbox.value);

                const exportTypes = Array.from(document.querySelectorAll('input[name="export_type[]"]:checked')).map(checkbox => checkbox.value);

                if (selectedRows.length === 0) {
                    notifyWarning('{{ __('trip-index.select-at-least-one-row') }}');
                    return;
                }

                // Cập nhật input hidden
                document.getElementById('selectedRows').value = JSON.stringify(selectedRows);

                // Gửi form
                this.submit();
            });
        </script>
    @endpush

@stop
