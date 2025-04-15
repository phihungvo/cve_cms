<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Action;

use App\Domains\User\Role\Feature\Model\Feature as Model;

class Create extends ActionAbstract
{
    protected array $data;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->createFeature();
    }

    protected function createFeature(): Model
    {
        $this->row = Model::query()->create([
            'alias' => $this->data['alias'],
            'name' => $this->data['name'],
            'description' => $this->data['description'],
        ]);


        return $this->row;
    }
}