{{--Start phan chung UI Create/Update--}}
<div class="box p-5 mt-5" {{auth()->user()->enterprise_id == null ?  '' : 'style=display:none'}}>
    @if( auth()->user()->enterprise_id ==null )
        <div class="p-2">
            <x-select name="enterprise_id" :options="$enterprises" value="id"
                      id="playlist-group-create-enterprise" class="cursor-pointer"
                      text="name" id="playlist-group-create-enterprise"
                      placeholder="{{__('playlist-group-create.select-enterprise')}}"
                      :label="__('playlist-group-create.enterprise')"
                      :readonly="$ROUTE == 'playlist_group.update'"
                      :disabled="$ROUTE == 'playlist_group.update'">
            </x-select>
        </div>
    @endif
    @if(isset($isUpdate))
        <input type="hidden" name="_action" value="update"/>
    @endif
</div>
<div class="box p-5 mt-5">
    <!-- name -->
    <div class="p-2">
        <span class="text-red-500">*</span>
        <label class="form label" for="name">{{__('playlist-group-create.name')}}</label>
        <div class="input-group">
            <input type="text" name="name" id="name" class="form-control form-control-lg"
                   placeholder="{{__('playlist-group-create.name')}}" required
                   value="{{ old('name', $REQUEST->input('name')?? '') }}">
        </div>
    </div>
    <!-- description -->
    <div class="p-2">
        <label class="form label" for="description">{{__('playlist-group-create.description')}}</label>
        <div class="input-group">
            <input type="text" name="description" id="description" class="form-control form-control-lg"
                   placeholder="{{__('playlist-group-create.description')}}"
                   value="{{ old('description', $REQUEST->input('description')?? '') }}">
        </div>
    </div>
</div>

{{--End phan chung UI Create/Update--}}
