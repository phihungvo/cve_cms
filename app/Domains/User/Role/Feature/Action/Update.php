<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Action;

use App\Domains\User\Role\Feature\Model\Feature as Model;

class Update extends ActionAbstract
{
    protected array $data;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->updateFeature();
    }

    protected function updateFeature(): Model
    {
        $this->row->update([
            'alias' => $this->data['alias'],
            'name' => $this->data['name'],
            'description' => $this->data['description'],
        ]);

        return $this->row;
    }
}