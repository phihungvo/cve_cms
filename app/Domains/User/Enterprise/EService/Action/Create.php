<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Action;

use App\Domains\User\Enterprise\EService\Model\EService as Model;
use Illuminate\Support\Facades\Log;

class Create extends ActionAbstract
{
    protected array $data;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->createPermission();
    }

    protected function createPermission(): Model
    {


        // Chuẩn bị mảng dữ liệu cơ bản
        $dataToCreate = [
            'alias' => $this->data['alias'],
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'enterprise_id' => $this->data['enterprise_id'],
            'pricing_model' => $this->data['pricing_model'],
            'price_per_unit' => $this->data['price_per_unit'],
            'billing_cycle' => $this->data['billing_cycle'],
            'max_unit' => $this->data['max_unit'],
            'note' => $this->data['note'],
        ];


        $this->row = Model::query()->create($dataToCreate);

        return $this->row;
    }
}