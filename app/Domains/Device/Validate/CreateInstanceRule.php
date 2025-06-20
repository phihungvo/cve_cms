<?php declare(strict_types=1);

namespace App\Domains\Device\Validate;

use App\Domains\Core\Validate\ValidateAbstract;
use App\Domains\Device\Enums\DetectedObject;
use App\Domains\Device\Enums\Direction;
use App\Domains\Device\Enums\RuleType;

class CreateInstanceRule extends ValidateAbstract
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'detected_object' => ['required', 'array', 'min:1'],
            'detected_object.*' => ['in:'.implode(',', array_column(DetectedObject::cases(), 'value'))],
            'rule_type' => ['required', 'in:'.implode(',', array_column(RuleType::cases(), 'value'))],
            'drawing_object' => ['nullable', 'array'],
            'direction' => ['nullable', 'in:'.implode(',', array_column(Direction::cases(), 'value'))],
            'device_cvedixrt_instance_id' => ['required', 'integer', 'exists:device_cvedixrt_instance,id'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }
}
