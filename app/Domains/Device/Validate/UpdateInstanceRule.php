<?php declare(strict_types=1);

namespace App\Domains\Device\Validate;

class UpdateInstanceRule extends CreateInstanceRule
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'uuid' => 'required|uuid',
        ]);
    }
}
