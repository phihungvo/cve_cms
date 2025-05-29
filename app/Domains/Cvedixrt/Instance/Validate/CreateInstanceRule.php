<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class CreateInstanceRule extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'size:36'],
            'name' => ['required', 'string', 'max:255'],
            'detected_object' => ['required', 'json'],
            'rule_type' => ['required', 'in:intrusion detection,area enter/exit,loitering,crowding,line crossing'],
            'drawing_object' => ['nullable', 'json'],
            'direction' => ['nullable', 'string', 'max:12'],
            'cvedixrt_instance_id' => ['required', 'integer', 'exists:cvedixrt_instance,id'],
        ];
    }
}
