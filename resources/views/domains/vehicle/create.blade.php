@extends ('domains.vehicle.index-layout')

@section ('content')

@if ($users_multiple)

<div class="box p-5 mt-5">
    <div class="p-2">
        <form method="get">
            @if (isset($enterprises))
                @if(auth()->user()->enterprise_id == null)
                <!-- Root thấy dropdown để chọn enterprise -->
                <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                          id="device-create-enterprise" class="cursor-pointer"
                          :label="__('device-create.enterprise')" data-change-submit
                          :placeholder="__('device-create.enterprise-select')"
                          :selected="$REQUEST->input('enterprise_id', $row->enterprise_id ?? null)"
                ></x-select>
                @endif
            @else
                <!-- User thường chỉ thấy tên enterprise -->
                <label for="device-enterprise"
                       class="form-label">{{ __('device-create.enterprise') }}</label>
                <input type="text" class="form-control form-control-lg" id="device-enterprise"
                       value="{{ $enterprise_name ?? 'N/A' }}" readonly>
                <input type="hidden" name="enterprise_id"
                       value="{{ $enterprise_id ?? $row->enterprise_id ?? '' }}">
            @endif
            <div class="mt-2"></div>
            <x-select name="user_id" :options="$customUsers" value="id" text="name"
                      id="vehicle-create-user" class="cursor-pointer"
                      :label="__('vehicle-create.user')"
                      :selected="$REQUEST->input('user_id')"
                      :placeholder="__('--select User--')"
                      form="vehicle-create-form"
                      required>
            </x-select>
        </form>
    </div>
</div>

@endif

<form method="post" id="vehicle-create-form">
    <input type="hidden" name="_action" value="create" />
{{--    <input type="hidden" name="user_id" value="{{ $user->id }}" />--}}

    @include ('domains.vehicle.molecules.create-update')

    <div class="box p-5 mt-5">
        <div class="text-right">
            <button type="submit" class="btn btn-primary">{{ __('vehicle-create.save') }}</button>
            <a href="{{ route('vehicle.index') }}"
               class="btn btn-secondary ml-5">{{ __('vehicle-update.cancel') }}</a>
        </div>
    </div>
</form>

@stop
