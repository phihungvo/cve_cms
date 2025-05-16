<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class Create extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'uuid' => ['bail', 'required', 'uuid'],
            'name' => ['bail', 'required', 'string'],
            'solution_id' => ['bail', 'required', 'integer'],
            'group_id' => ['bail', 'required', 'integer'],
            // đảm bảo có ít nhất 1 trong 2 trường camera_id hoặc input_source_text
            'uri' => ['bail', 'nullable', 'string', 'required_without:input_source_text'], // truyen uri
            'input_source_text' => ['bail', 'nullable', 'string', 'required_without:uri'],
            'description' => ['bail', 'nullable', 'string'],
        ];
    }
}
