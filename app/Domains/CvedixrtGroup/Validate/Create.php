<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class Create extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ];
    }
}
