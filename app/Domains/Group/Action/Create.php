<?php declare(strict_types=1);

namespace App\Domains\Group\Action;

use App\Domains\Group\Model\DeviceCvedixrtGroup as Model;

class Create extends CreateUpdateAbstract
{

    /**
     * Save the new group data.
     *
     * @return void
     *
     * @override
     */
    protected function save(): void
    {
        $this->row = Model::query()->create([
            'group_name' => $this->data['group_name'],
            'description' => $this->data['description'],
        ]);
    }
}
