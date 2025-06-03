{{--Start phan chung UI Create/Update--}}
<div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
    <div class="flex justify-between items-center mb-4">
        @if(isset($row))
            <h2 class="text-2xl font-bold">{{ __('Update instance') }}</h2>
            <a href="{{ route('cvedixrt_instance.analytics', ['id' => $row->id]) }}"
               class="inline-block text-primary p-2 font-bold"
               onmouseover="this.style.textDecoration='underline'"
               onmouseout="this.style.textDecoration='none'">
                {{ __('Analytic Rule') }}
            </a>
        @else
            <h2 class="text-2xl font-bold">{{ __('Create instance') }}</h2>
        @endif
    </div>

    <!-- UUID -->
    <div class="mt-2">
        <span class="text-red-500">*</span>
        <label class="form-label">{{__('cvedixrt-instance-create.uuid')}}</label>
        <div class="input-group">
            <input type="text" name="uuid" class="form-control form-control-lg" required readonly
                   value="{{old('uuid', isset($row) ? $row->uuid : $uuid)}}" id="instance_uuid">
            <button type="button" class="input-group-text input-group-text-lg"
                    style="padding-top: 1px; padding-bottom: 1px"
                    title="{{__('common.generate')}}"
                    data-password-generate="#instance_uuid"
                    data-password-generate-format="uuid"
                    tabindex="-1">@icon('refresh-cw', 'w-5 h-5')
            </button>
        </div>
    </div>

    <!-- Name -->
    <div class="mt-2">
        <span class="text-red-500">*</span>
        <label class="form-label">{{__('cvedixrt-instance-create.name')}}</label>
        <input type="text" name="name" class="form-control form-control-lg" required
               value="{{old('name', isset($row) ?$row->name : '')}}">
    </div>

    <!-- select instance solution -->
    <div class="mt-2">
        <span class="text-red-500">*</span>
        <x-select name="solution_id" :options="$solutions" value="id" text="name"
                  id="" class="cursor-pointer"
                  :label="__('cvedixrt-instance-create.solution')"
                  :placeholder="__('cvedixrt-instance-create.solution-select')"
                  :selected="old('solution_id', isset($row) ? $row->solution_id : null)"
                  required>
        </x-select>
        <div class="mt-1">
            <!--Btn Create Solution -->
            <a href="{{route('cvedixrt_solution.create')}}"
               class="inline-block text-primary p-2 font-bold"
               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                {{__('cvedixrt-instance-create.btn-create-solution')}}</a>
        </div>
    </div>

    <!-- select instance group -->
    <div class="mt-2">
        <span class="text-red-500">*</span>
        <x-select name="group_id" :options="$groups" value="id" text="name"
                  id="" class="cursor-pointer"
                  :label="__('cvedixrt-instance-create.group')"
                  :placeholder="__('cvedixrt-instance-create.group-select')"
                  :selected="old('group_id', isset($row) ? $row->group_id : null)"
                  required>
        </x-select>
        <div class="mt-1">
            <!--Btn Create Group -->
            <a href="{{route('cvedixrt_group.create')}}"
               class="inline-block text-primary p-2 font-bold"
               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                {{__('cvedixrt-instance-create.btn-create-group')}}</a>
        </div>
    </div>

</div>
{{--End phan chung UI Create/Update--}}
