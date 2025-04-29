

{{--@dd(get_defined_vars())--}}
<div class="box p-5 mt-5" {{auth()->user()->isRoleRoot() ?  '' : 'style=display:none'}}>
    @if(auth()->user()->isRoleRoot())
        <div class="p-2">
            <label class="form label" for="enterprise_select">{{__('Enterprise')}}</label>
            <div class="input-group">
                <select name="enterprise_id" id="enterprise_select" class="form-control form-control-lg">
                    <option value="">{{__('-- Select Enterprise --')}}</option>
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
        <input type="hidden" name="_action" value="update" />
    @endif
</div>
<div class="box-5 mt-5">
    <!-- name -->
    <div class="p-2">
        <label class="form label" for="name">{{__('Name')}}</label>
        <div class="input-group">
            <input type="text" name="name" id="name" class="form-control form-control-lg"
                   placeholder="{{__('Name')}}"
                   value="{{ old('name', $REQUEST->input('name')?? '') }}">
        </div>
    </div>
    <!-- description -->
    <div class="p-2">
        <label class="form label" for="description">{{__('Description')}}</label>
        <div class="input-group">
            <input type="text" name="description" id="description" class="form-control form-control-lg"
                   placeholder="{{__('Description')}}"
                   value="{{ old('description', $REQUEST->input('description')?? '') }}">
        </div>
    </div>
</div>
