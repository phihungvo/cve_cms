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

        </div>
        @endif

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
