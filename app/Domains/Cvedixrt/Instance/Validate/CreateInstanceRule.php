<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Validate;

use App\Domains\Core\Validate\ValidateAbstract;
use App\Domains\Cvedixrt\Instance\Enums\DetectedObject;
use App\Domains\Cvedixrt\Instance\Enums\Direction;
use App\Domains\Cvedixrt\Instance\Enums\RuleType;

class CreateInstanceRule extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'size:36'],
            'name' => ['required', 'string', 'max:255'],
            'detected_object' => ['required', 'array', 'min:1'],
            'detected_object.*' => ['in:'.implode(',', array_column(DetectedObject::cases(), 'value'))],
            'rule_type' => ['required', 'in:'.implode(',', array_column(RuleType::cases(), 'value'))],
            'drawing_object' => ['nullable', 'array'],
            'direction' => ['nullable', 'in:'.implode(',', array_column(Direction::cases(), 'value'))],
            'cvedixrt_instance_id' => ['required', 'integer', 'exists:cvedixrt_instance,id'],
        ];
    }
}
