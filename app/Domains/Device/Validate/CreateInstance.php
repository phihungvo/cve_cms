<?php

declare(strict_types=1);

namespace App\Domains\Device\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class CreateInstance extends ValidateAbstract
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'uuid' => ['bail', 'required', 'uuid'],
            'instance_name' => ['bail', 'required', 'string'],
            'solution_id' => ['bail', 'required', 'integer'],
            'group_id' => ['bail', 'required', 'integer'],
        ];
    }
}
