<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Action;

use App\Domains\User\Enterprise\Billing\Model\Billing as Model;
use Illuminate\Support\Facades\Log;

class Create extends ActionAbstract
{
    protected array $data;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->createBilling();
    }

    protected function createBilling(): Model
    {
        // Chuẩn bị mảng dữ liệu để tạo Billing
        $dataToCreate = [
            'name' => $this->data['name'],
            'license_id' => $this->data['license_id'],
            'start_date' => $this->data['start_date'],
            'end_date' => $this->data['end_date'],
            'usage_unit' => $this->data['usage_unit'],
            'payment_status' => $this->data['payment_status'],
            'price' => $this->data['price'],
        ];

        $this->row = Model::query()->create($dataToCreate);

        return $this->row;
    }
}