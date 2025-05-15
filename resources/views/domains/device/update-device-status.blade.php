@extends ('domains.device.update-layout')

@section ('content_inner')
    <div class="p-0 py-5 bg-light-blue">
        <div class="box shadow-sm rounded-lg p-4">
            @if (empty($infoDevice->deviceStatus->data))
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">{{ __('device-status.status_title') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('device-status.updating') }}</p>
                </div>
            @else
                <div class="p-2">
                    <!-- MQTT Status -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.mqtt_status') }}</label>
                        <input type="text" class="form-control"
                            value="{{ ($infoDevice->deviceStatus->data['MQTT']['connected'] ?? false) ? __('device-status.mqtt_connected') : __('device-status.mqtt_disconnected') }}"
                            readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.mqtt_configured_label') }}</label>
                        <input type="text" class="form-control"
                            value="{{ ($infoDevice->deviceStatus->data['MQTT']['configured'] ?? false) ? __('device-status.mqtt_configured') : __('device-status.mqtt_not_configured') }}"
                            readonly>
                    </div>

                    <!-- Device Status -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.fppd_status') }}</label>
                        <input type="text" class="form-control"
                            value="{{ $infoDevice->deviceStatus->data['fppd'] ?? 'unknown' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.mode') }}</label>
                        <input type="text" class="form-control"
                            value="{{ ($infoDevice->deviceStatus->data['mode_name'] ?? 'unknown') . ' (mode: ' . ($infoDevice->deviceStatus->data['mode'] ?? 'N/A') . ')' }}"
                            readonly>
                    </div>

                    <!-- Time and Uptime -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.current_time') }}</label>
                        <input type="text" class="form-control"
                            value="{{ ($infoDevice->deviceStatus->data['dateStr'] ?? '') . ' ' . ($infoDevice->deviceStatus->data['timeStr'] ?? '') }}"
                            readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.uptime') }}</label>
                        <input type="text" class="form-control"
                            value="{{ ($infoDevice->deviceStatus->data['uptimeStr'] ?? 'N/A') . ' (' . ($infoDevice->deviceStatus->data['uptime'] ?? 'N/A') . ')' }}"
                            readonly>
                    </div>

                    <hr class="my-4">

                    <!-- WiFi Status -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.wifi_status') }}</label>
                        @if (isset($infoDevice->deviceStatus->data['wifi']) && is_array($infoDevice->deviceStatus->data['wifi']))
                            <table class="table table-bordered mt-2 border-separate border">
                                <thead>
                                    <tr>
                                        <th class="border-r">{{ __('device-status.interface') }}</th>
                                        <th class="border-r">{{ __('device-status.signal_strength') }}</th>
                                        <th class="border-r">{{ __('device-status.description') }}</th>
                                        <th class="border-r">{{ __('device-status.link_quality') }}</th>
                                        <th class="border-r">{{ __('device-status.signal_level') }}</th>
                                        <th>{{ __('device-status.noise') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($infoDevice->deviceStatus->data['wifi'] as $wifi)
                                        <tr>
                                            <td class="border-r">{{ $wifi['interface'] ?? 'N/A' }}</td>
                                            <td class="border-r">{{ $wifi['pct'] ?? 'N/A' }}</td>
                                            <td class="border-r">{{ $wifi['desc'] ?? 'N/A' }}</td>
                                            <td class="border-r">{{ $wifi['link'] ?? 'N/A' }}</td>
                                            <td class="border-r">{{ $wifi['level'] ?? 'N/A' }}</td>
                                            <td>{{ $wifi['noise'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <input type="text" class="form-control" value="N/A" readonly>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- Network Interfaces -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.network_interfaces') }}</label>
                        @if (isset($infoDevice->deviceStatus->data['interfaces']) && is_array($infoDevice->deviceStatus->data['interfaces']))
                            <table class="table table-bordered mt-2 border-separate border">
                                <thead>
                                    <tr>
                                        <th class="border-r">{{ __('device-status.interface') }}</th>
                                        <th class="border-r">{{ __('device-status.ip_address') }}</th>
                                        <th>{{ __('device-status.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($infoDevice->deviceStatus->data['interfaces'] as $interface)
                                        <tr>
                                            <td class="border-r">{{ $interface['ifname'] ?? 'N/A' }}</td>
                                            <td class="border-r">{{ $interface['addr_info'][0]['local'] ?? 'N/A' }}</td>
                                            <td>{{ $interface['operstate'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <input type="text" class="form-control" value="N/A" readonly>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- System Details -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.system_details') }}</label>
                        <table class="table table-bordered mt-2 border-separate border">
                            <tbody>
                                <tr>
                                    <th class="border-r">{{ __('device-status.branch') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['branch'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="border-r">{{ __('device-status.version') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['version'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="border-r">{{ __('device-status.platform') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['platform'] ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <!-- Device Identity -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.device_identity') }}</label>
                        <table class="table table-bordered mt-2 border-separate border">
                            <tbody>
                                <tr>
                                    <th class="border-r">{{ __('device-status.uuid') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['uuid'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="border-r">{{ __('device-status.hostname') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['host_name'] ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <!-- Sensors -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.sensors') }}</label>
                        @if (isset($infoDevice->deviceStatus->data['sensors']) && is_array($infoDevice->deviceStatus->data['sensors']))
                            <table class="table table-bordered mt-2 border-separate border">
                                <thead>
                                    <tr>
                                        <th class="border-r">{{ __('device-status.label') }}</th>
                                        <th class="border-r">{{ __('device-status.value') }}</th>
                                        <th>{{ __('device-status.type') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($infoDevice->deviceStatus->data['sensors'] as $sensor)
                                        <tr>
                                            <td class="border-r">{{ $sensor['label'] ?? 'N/A' }}</td>
                                            <td class="border-r">{{ $sensor['formatted'] ?? 'N/A' }}</td>
                                            <td>{{ $sensor['valueType'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <input type="text" class="form-control" value="N/A" readonly>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- Scheduler -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.scheduler') }}</label>
                        <table class="table table-bordered mt-2 border-separate border">
                            <tbody>
                                <tr>
                                    <th class="border-r">{{ __('device-status.status') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['scheduler']['status'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="border-r">{{ __('device-status.enabled') }}</th>
                                    <td>{{ isset($infoDevice->deviceStatus->data['scheduler']['enabled']) ? ($infoDevice->deviceStatus->data['scheduler']['enabled'] ? 'Yes' : 'No') : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="border-r">{{ __('device-status.next_playlist') }}</th>
                                    <td>{{ $infoDevice->deviceStatus->data['scheduler']['nextPlaylist']['playlistName'] ?? 'N/A' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <!-- Additional Fields -->
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.volume') }}</label>
                        <input type="text" class="form-control" value="{{ $infoDevice->deviceStatus->data['volume'] ?? 'N/A' }}"
                            readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold">{{ __('device-status.status') }}</label>
                        <input type="text" class="form-control"
                            value="{{ $infoDevice->deviceStatus->data['status_name'] ?? ($infoDevice->deviceStatus->data['status'] ?? 'N/A') }}"
                            readonly>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
