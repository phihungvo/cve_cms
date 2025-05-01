<?php declare(strict_types=1);

namespace App\Domains\Group\Action;

class Update extends CreateUpdateAbstract
{

    /**
     * Save the updated group data.
     *
     * @return void
     *
     * @overide
     */
    protected function save(): void
    {
        $this->row->update([
            'group_name' => $this->data['group_name'],
            'description' => $this->data['description'],
        ]);
    }
}
