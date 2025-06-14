<?php

declare(strict_types=1);

namespace App\Domains\Device\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class Create extends ValidateAbstract
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'device_type_id' => ['bail', 'required', 'integer'],
            'enterprise_id' => ['bail', 'nullable', 'integer'],
            'code' => ['bail', 'required', 'uuid'],
            'name' => ['bail', 'required', 'string'],
            'model' => ['bail', 'required', 'string'],
            'serial' => ['bail', 'required', 'string'],
            'type' => ['bail', 'nullable', 'string'],
            'information' => ['bail', 'nullable', 'json'],
            'phone_number' => ['bail', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => ['bail', 'string'],
            'vehicle_id' => ['bail', 'nullable', 'integer'],
            'device_groups' => ['bail', 'nullable', 'array'],
            'device_group.*' => ['bail', 'integer'],
            'enabled' => ['bail', 'nullable', 'boolean'],
            'shared' => ['bail', 'boolean'],
            'shared_public' => ['bail', 'boolean'],
            'enable_ai' => ['bail', 'nullable', 'boolean'],
            'instance_maximum' => ['bail', 'nullable', 'integer', 'min:0'],
        ];
    }
}
