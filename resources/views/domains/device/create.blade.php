@extends ('layouts.in')

@section ('body')

@if ($users_multiple)

    <div class="box p-5 mt-5">
        <div class="p-2">
            <form method="get">
                <div class="mb-2">
                    @if (isset($enterprises)) <!-- Root thấy dropdown để chọn enterprise -->
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name" id="device-create-enterprise"
                              :label="__('device-create.enterprise')" :placeholder="__('device-create.enterprise-select')"
                              :selected="$REQUEST->input('enterprise_id', $row->enterprise_id ?? null)" required></x-select>
                    @else <!-- User thường chỉ thấy tên enterprise -->
                    <label for="device-enterprise" class="form-label">{{ __('device-create.enterprise') }}</label>
                    <input type="text" class="form-control form-control-lg" id="device-enterprise"
                           value="{{ $enterprise_name ?? 'N/A' }}" readonly>
                    <input type="hidden" name="enterprise_id" value="{{ $enterprise_id ?? $row->enterprise_id ?? '' }}">
                    @endif
                </div>

                <x-select name="user_id" :options="$users" value="id" text="name" id="device-create-user"
                    :label="__('device-create.user')" :selected="$REQUEST->input('user_id')" data-change-submit
                    required></x-select>
            </form>
        </div>
    </div>

@endif

<form method="post">
    <input type="hidden" name="_action" value="create" />
    <input type="hidden" name="user_id" value="{{ $user->id }}" />

    @include ('domains.device.molecules.create-update')

    <div class="box p-5 mt-5">
        <div class="text-right">
            <button type="submit" class="btn btn-primary">{{ __('device-create.save') }}</button>
            <a href="{{ route('device.index') }}" class="btn btn-secondary ml-2">{{ __('device-create.cancel') }}</a>
        </div>
    </div>
</form>

@stop
