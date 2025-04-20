@extends ('layouts.in')

@section ('body')
    <form method="post">
        <input type="hidden" name="_action" value="create"/>

        @if(auth()->user()->isRoot())
        <div class="box p-5 mt-5">
            @if (isset($enterprises))
                <!-- Root thấy dropdown để chọn enterprise -->
                <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                          id="device-create-enterprise"
                          :label="__('device-create.enterprise')"
                          :placeholder="__('device-create.enterprise-select')"
                          :selected="$REQUEST->input('enterprise_id', $row->enterprise_id ?? null)"
                ></x-select>
            @else
                <!-- User thường chỉ thấy tên enterprise -->
                <label for="device-enterprise"
                       class="form-label">{{ __('device-create.enterprise') }}</label>
                <input type="text" class="form-control form-control-lg" id="device-enterprise"
                       value="{{ $enterprise_name ?? 'N/A' }}" readonly>
                <input type="hidden" name="enterprise_id"
                       value="{{ $enterprise_id ?? $row->enterprise_id ?? '' }}">
            @endif

            <!-- select box users -->
{{--            <div class="mt-2">--}}
{{--                <label for="device-user" class="form-label">{{ __('device-create.user') }}</label>--}}
{{--                <select name="user_id" id="device-user" class="form-select form-select-lg bg-white">--}}
{{--                    <option value="">{{ __('device-create.user-select') }}</option>--}}
{{--                    @foreach ($listUser as $user)--}}
{{--                        <option--}}
{{--                            value="{{ $user['id'] }}"--}}
{{--                            {{ old('user_id', $REQUEST->input('user_id', $row->user_id ?? null)) == $user['id'] ? 'selected' : '' }}>--}}
{{--                            {{ $user['name'] }}--}}
{{--                        </option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
        </div>
        @endif

{{--        @if(!auth()->user()->isRoot())--}}
{{--            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}"/>--}}
{{--        @endif--}}

        @include ('domains.device.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{ __('device-create.save') }}</button>
                <a href="{{ route('device.index') }}"
                   class="btn btn-secondary ml-2">{{ __('device-create.cancel') }}</a>
            </div>
        </div>
    </form>

@stop
@push('scripts')
    <script>
        let selectEnterprise = document.getElementById('device-create-enterprise');
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
