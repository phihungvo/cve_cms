<div class="box p-5 mt-5" {{ auth()->user()->enterprise_id == null ? '' : 'style=display:none' }}>
    @if(auth()->user()->enterprise_id == null)
        <div class="p-2">
            <x-select name="enterprise_id" :options="$enterprises" value="id"
                      id="playlist-group-create-enterprise" class="cursor-pointer"
                      text="name" id="playlist-group-create-enterprise"
                      placeholder="{{__('schedule-group-create.select-enterprise')}}"
                      :label="__('playlist-group-create.enterprise')"
                      :readonly="old('enterprise_id', $REQUEST->input('enterprise_id')) && $ROUTE == 'schedule_group.update'"
                      :disabled="old('enterprise_id', $REQUEST->input('enterprise_id')) && $ROUTE == 'schedule_group.update'">
            </x-select>
            </div>
        </div>
    @endif
    @if(isset($isUpdate))
        <input type="hidden" name="_action" value="update">
    @endif
</div>
<div class="box p-5 mt-5">
    <!-- name -->
    <div class="p-2">
        <span class="text-red-500">*</span>
        <label class="form label" for="name">{{__('schedule-group-create.name')}}</label>
        <div class="input-group">
            <input type="text" name="name" id="name" class="form-control form-control-lg"
                   placeholder="{{__('schedule-group-create.name')}}" required
                   value="{{ old('name', $REQUEST->input('name')?? '') }}">
        </div>
    </div>
    <!-- description -->
    <div class="p-2">
        <label class="form label" for="description">{{__('schedule-group-create.description')}}</label>
        <div class="input-group">
            <input type="text" name="description" id="description" class="form-control form-control-lg"
                   placeholder="{{__('schedule-group-create.description')}}"
                   value="{{ old('description', $REQUEST->input('description')?? '') }}">
        </div>
    </div>
</div>
{{--End phan chung UI Create/Update--}}
