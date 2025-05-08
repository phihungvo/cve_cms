<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Action;

use App\Domains\User\Enterprise\Billing\Model\Billing as Model;

class Update extends ActionAbstract
{
    protected array $data;
    protected Model $services;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->updateBilling();
    }

    protected function updateBilling(): Model
    {


        // Chuẩn bị mảng dữ liệu cơ bản
        $dataToUpdate = [
            'service_id' => $this->data['service_id'],
            'enterprise_id' => $this->data['enterprise_id'],
            'license_type' => $this->data['license_type'],
            'max_users' => $this->data['max_users'],
            'max_devices' => $this->data['max_devices'],
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
            'status' => $this->data['status'],
            'license_key' => $this->data['license_key'],
        ];

        $this->row->update($dataToUpdate);

        return $this->row;
    }
}