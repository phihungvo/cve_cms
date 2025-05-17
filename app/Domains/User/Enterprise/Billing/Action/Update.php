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
            'name' => $this->data['name'],
            'license_id' => $this->data['license_id'],
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
            'usage_unit' => $this->data['usage_unit'],
            'payment_status' => $this->data['payment_status'],
            'price' => $this->data['price'],
        ];

        $this->row->update($dataToUpdate);

        return $this->row;
    }
}