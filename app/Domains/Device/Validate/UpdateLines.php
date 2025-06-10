<?php declare(strict_types=1);

namespace App\Domains\Device\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class UpdateLines extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'lines' => ['array'],
        ];
    }
}
