<div class="box p-5 mt-5">
    {{--    {{dd($enterprises)}}--}}
    <div class="p-2">
        <label for="enterprise_name" class="form-label">{{__('enterprise-create.name')}}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="enterprise_name"
               value="{{ old('name', $enterprises['name']) }}" required>
    </div>
    <div class="p-2">
        <label for="enterprise_code" class="form-label">{{__('enterprise-create.code')}}</label>
        <input type="text" name="code" class="form-control form-control-lg" id="enterprise_code"
               value="{{ old('code', $enterprises['code'])  }}" required>
    </div>
    <div class="p-2">
        <label for="enterprise_address" class="form-label">{{__('enterprise-create.address')}}</label>
        <input type="text" name="address" class="form-control form-control-lg" id="enterprise_address"
               value="{{ old('address', $enterprises['address']) }}" required>
    </div>
    <div class="p-2">
        <label for="enterprise_phone_number" class="form-label">{{__('enterprise-create.phone_number')}}</label>
        <input type="text" name="phone_number" class="form-control form-control-lg" id="enterprise_phone_number"
               value="{{ old('phone_number', $enterprises['phone_number']) }}" required>
    </div>
    <div class="p-2">
        <label for="enterprise_email" class="form-label">{{__('enterprise-create.email')}}</label>
        <input type="email" name="email" class="form-control form-control-lg" id="enterprise_email"
               value="{{ old('email', $enterprises['email']) }}" required/>
    </div>
    <div class="p-2">
        <label for="owner_id" class="form-label">{{__('enterprise-create.owner')}}</label>
        <select name="owner_id" class="form-control form-control-lg" id="owner_id" required>
            @foreach($users as $user)
                <option
                    value="{{ $user['id'] }}" {{ old('owner_id', $enterprises['owner_id']) == $user['id'] ? 'selected' : '' }}>
                    {{ $user['name'] }} ({{ $user['email'] }})
                </option>
            @endforeach
        </select>
    </div>

</div>


