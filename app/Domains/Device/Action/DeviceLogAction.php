<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\CoreApp\Action\ActionAbstract;
use App\Domains\Device\Model\DeviceLog;
use Illuminate\Validation\ValidationException;

class DeviceLogAction extends ActionAbstract
{
    public function create(): DeviceLog
    {
        // Validation
        $this->validate([
            'serial' => 'required|string',
            'type' => 'required|string',
            'description' => 'nullable|string',
            // 'data' => 'nullable|array',
            // 'status' => 'nullable|string',
            // 'error' => 'nullable|string',
        ]);

        return DeviceLog::create($this->data);
    }

    protected function validate(array $rules): void
    {
        $validator = validator($this->data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}