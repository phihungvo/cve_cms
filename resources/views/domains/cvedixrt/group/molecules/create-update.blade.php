{{--Start phan chung UI Create/Update--}}
<div class="box p-5 mt-5">
    <!-- name -->
    <div class="p-2">
        <span class="text-red-500">*</span>
        <label class="form label" for="name">{{__('cvedixrt-group-create.name')}}</label>
        <div class="input-group">
            <input type="text" name="name" id="name" class="form-control form-control-lg"
                   placeholder="{{__('cvedixrt-group-create.name')}}" required
                   value="{{ old('name', $REQUEST->input('name')?? '') }}">
        </div>
    </div>
    <!-- description -->
    <div class="p-2">
        <label class="form label" for="description">{{__('cvedixrt-group-create.description')}}</label>
        <div class="input-group">
            <input type="text" name="description" id="description" class="form-control form-control-lg"
                   placeholder="{{__('cvedixrt-group-create.description')}}"
                   value="{{ old('description', $REQUEST->input('description')?? '') }}">
        </div>
    </div>
</div>
{{--End phan chung UI Create/Update--}}
