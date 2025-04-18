@extends ('domains.device.update-layout')

@section ('content')
    <form method="post">
        <input type="hidden" name="_action" value="update"/>
        @if(auth()->user()->isRoleRoot())
            <div class="box p-5 mt-5">
                {{--                    Kiểm tra role root--}}
                @if (isset($enterprises))
                    <!-- select enterprises -->
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                              id="device-update-enterprise"
                              :label="__('device-create.enterprise')"
                              :placeholder="__('device-create.enterprise-select')"
                              :selected="$REQUEST->input('enterprise_id', $row->enterprise_id ?? null)">
                    </x-select>
                @endif

                <!-- select box users -->
{{--                <div class="form-group">--}}
{{--                    <label for="device-update-user" class="form-label">{{ __('device-create.user') }}</label>--}}
{{--                    <select name="user_id" id="device-user" class="form-select form-select-lg bg-white"--}}
{{--                    >--}}
{{--                        <option value="">{{ __('device-create.user-select') }}</option>--}}
{{--                        @foreach ($listUser as $user)--}}
{{--                            <option--}}
{{--                                value="{{ $user['id'] }}" {{ $REQUEST->input('user_id') == $user['id'] ? 'selected' : '' }}>--}}
{{--                                {{ $user['name'] }}--}}
{{--                            </option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                </div>--}}
            </div>
        @endif

        @if(!auth()->user()->isRoot())
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}"/>
        @endif

        @include ('domains.device.molecules.create-update')

        @if ($row->shared)

            <div class="box p-5 mt-5">
                <div class="p-2">
                    <span class="font-medium">{{ __('device-update.shared-url') }}</span> <a
                        href="{{ route('shared.device', $row->code) }}" class="text-primary"
                        target="_blank">{{ route('shared.device', $row->code) }}</a>
                </div>
            </div>

        @endif

        <div class="box p-5 mt-5">
            <div class="text-right">
                <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                   class="btn btn-outline-danger mr-2">{{ __('device-update.delete-button') }}</a>
                <button type="submit" class="btn btn-primary"
                        data-click-one>{{ __('device-update.save') }}</button>
                <a href="{{ route('device.index') }}"
                   class="btn btn-secondary ml-2">{{ __('device-create.cancel') }}</a>
            </div>
        </div>
    </form>

    @include ('molecules.delete-modal', [
        'title' => __('device-update.delete-title'),
        'message' => __('device-update.delete-message'),
    ])

@stop
@push('scripts')
    <script>
        let selectEnterprise = document.getElementById('device-update-enterprise');
        selectEnterprise.addEventListener('change', function () {
            const selectedValue = this.value;
            const currentDomain = window.location.origin + window.location.pathname;
            const params = new URLSearchParams(window.location.search);

            // Update the query parameter
            params.set('enterprise_id', selectedValue);

            // Redirect with updated query parameters
            window.location.href = `${currentDomain}?${params.toString()}`;
        });

        let selectUser = document.getElementById('device-user');
        selectUser.addEventListener('change', function () {
            const selectedValue = this.value;
            const currentDomain = window.location.origin + window.location.pathname;
            const params = new URLSearchParams(window.location.search);

            // Update the query parameter
            params.set('user_id', selectedValue);

            // Redirect with updated query parameters
            window.location.href = `${currentDomain}?${params.toString()}`;
        });
    </script>
@endpush
