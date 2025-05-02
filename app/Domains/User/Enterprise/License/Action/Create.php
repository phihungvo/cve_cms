<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Action;

use App\Domains\User\Enterprise\License\Model\License as Model;
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
        ];


        $this->row = Model::query()->create($dataToCreate);

        return $this->row;
    }
}