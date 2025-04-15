@extends('layouts.in')

@section('body')
    <div class="intro-y box p-5">
        <form method="POST" action="{{ route('user.role.update', $role->id) }}">
            @csrf
            @method('PUT')

            <!-- Role Name -->
            <div class="form-group mb-4">
                <label class="form-label required">{{ __('role-create.Name') }}</label>
                <input type="text" name="name"
                       class="form-control form-control-lg {{ $errors->has('name') ? 'border-red-500' : '' }}"
                       value="{{ old('name', $role->name) }}" required>
                @if ($errors->has('name'))
                    <div class="text-red-500 mt-1">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <!-- Permission Selection -->
            <div class="form-group mb-4">
                <label class="form-label">{{ __('role-create.Permissions') }}</label>
                @php
                    $groupPermissions = $permissions->groupBy(function ($item) {
                        return explode(' ', $item->name)[1] ?? $item->name;
                    });
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($groupPermissions as $groupName => $permissions)
                        <div class="border rounded-lg shadow-sm p-4 min-w-[300px]">
                            <strong class="block mb-2">{{ $groupName }}</strong> <!-- Tiêu đề nhóm -->
                            @foreach ($permissions as $permission)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" name="permission_ids[]"
                                           value="{{ $permission->id }}"
                                           class="form-check-switch permission-checkbox group-{{$loop->parent->index}}"
                                           id="permission-{{$permission->id}}"
                                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                    <label for="permission-{{$permission->id}}"
                                           class="ml-2">{{ $permission->name }} ({{$permission->description}})</label>
                                </div>
                            @endforeach
                            <!-- Checkbox chính để check/uncheck tất cả -->
                            <div class="flex items-center mb-2">
                                <input type="checkbox" name="" id="group-{{$loop->index}}" class="form-check-switch group-checkbox">
                                <label for="group-{{$loop->index}}" class="ml-2">Check all</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2 mt-5">
                <a href="{{ route('user.role.index') }}" class="btn bg-white">
                    {{ __('common.Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('role-update.Save') }}
                </button>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
<script>
    <!-- Check/uncheck all checkboxes in a group -->
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".group-checkbox").forEach(groupCheckbox => {
            groupCheckbox.addEventListener("change", function () {
                let groupIndex = this.id.replace("group-", "");
                let checkboxes = document.querySelectorAll(".group-" + groupIndex);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });
    });
</script>
@endpush

