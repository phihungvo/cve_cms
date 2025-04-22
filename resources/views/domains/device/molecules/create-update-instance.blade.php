
{{--khai bao phan chung cua 2 view create update--}}
{{--@dd(get_defined_vars())--}}
    {{--                        start phan chung--}}
    <div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
        <!-- UUID -->
        <div>
            <span class="text-red-500">*</span>
            <label class="form-label">UUID</label>
            <div class="input-group">
                <input type="text" name="uuid" class="form-control" required readonly
                       value="{{old('uuid', isset($instance) ? $instance->uuid : $uuid)}}" id="instance_uuid">
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
        <div>
            <span class="text-red-500">*</span>
            <label class="form-label">{{__('cvedit-instance.name')}}</label>
            <input type="text" name="instance_name" class="form-control" required
                   value="{{old('instance_name', isset($instance) ?$instance->instance_name : '')}}">
        </div>

        <!-- select instance solution -->
        <div>
            <span class="text-red-500">*</span>
            <x-select name="solution_id" :options="$solutions" value="id" text="solution_name"
                      id=""
                      :label="__('rt-analytics-create.solution')"
                      :placeholder="__('rt-analytics-create.solution-select')"
                      :selected="old('solution_id', isset($instance) ? $instance->solution_id : null)"
                      required>
            </x-select>
        </div>
        <!-- select instance group -->
        <div>
            <span class="text-red-500">*</span>
            <x-select name="group_id" :options="$groups" value="id" text="group_name"
                      id=""
                      :label="__('rt-analytics-create.group')"
                      :placeholder="__('rt-analytics-create.group-select')"
                      :selected="old('group_id', isset($instance) ? $instance->group_id : null)"
                      required>
            </x-select>
        </div>
    </div>
    {{--                        end phan chung--}}
