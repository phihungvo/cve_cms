<?php

namespace App\Domains\User\Role\Service;

use App\Domains\User\Role\Model\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Thêm use này
use Illuminate\Validation\ValidationException;

class Create
{
    protected $data;

    public static function make(array $data): self
    {
        $instance = new self();
        $instance->data = $data;
        return $instance;
    }

    public function validate(): self
    {
        $validator = Validator::make($this->data, [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this;
    }

    public function create(): Role
    {
        // Tạo alias từ name
        $alias = Str::slug($this->data['name']);
        $originalAlias = $alias;
        $counter = 1;

        while (Role::where('alias', $alias)->exists()) {
            $alias = $originalAlias . '-' . $counter;
            $counter++;
        }

        return Role::create([
            'name' => $this->data['name'],
            'enterprise_id' => $this->data['enterprise_id'] ?? null,
            'alias' => $alias,
        ]);
    }
}
