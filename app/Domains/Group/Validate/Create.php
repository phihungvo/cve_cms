<?php declare(strict_types=1);

namespace App\Domains\Group\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class Create extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
