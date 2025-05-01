<?php declare(strict_types=1);

namespace App\Domains\Solution\Validate;
use App\Domains\Core\Validate\ValidateAbstract;


class Create extends ValidateAbstract
{

    public function rules(): array
    {
        return [
            'solution_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
