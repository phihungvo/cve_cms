<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Action;

use App\Domains\User\Enterprise\EService\Model\EService as Model;

class Update extends ActionAbstract
{
    protected array $data;
    protected Model $services;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->updateEService();
    }

    protected function updateEService(): Model
    {


        // Chuẩn bị mảng dữ liệu cơ bản
        $dataToUpdate = [
            'alias' => $this->data['alias'],
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'enterprise_id' => $this->data['enterprise_id'],
            'pricing_model' => $this->data['pricing_model'],
            'price' => $this->data['price'],
            'billing_cycle' => $this->data['billing_cycle'],
            'max_unit' => $this->data['max_unit'],
            'note' => $this->data['note'],
        ];

        $this->row->update($dataToUpdate);

        return $this->row;
    }
}