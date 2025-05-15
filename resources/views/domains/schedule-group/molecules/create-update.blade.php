<div class="box p-5 mt-5" {{ auth()->user()->enterprise_id == null ? '' : 'style=display:none' }}>
    @if(auth()->user()->enterprise_id == null)
        <div class="p-2">
            <label>{{__('schedule-group-create.enterprise')}}</label>
            <div class="input-group">
                <select name="enterprise_id" id="enterprise_select" class="form-control form-control-lg">
                    <option value="">{{__('schedule-group-create.select-enterprise')}}</option>
                    @foreach ($enterprises as $enterprise)
                        <option value="{{ $enterprise->id }}"
                            {{ $REQUEST->input('enterprise_id') == $enterprise->id ? 'selected' : '' }}>
                            {{ $enterprise->name }}
                        </option>
                    @endforeach
                </select>
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
