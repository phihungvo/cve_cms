<?php declare(strict_types=1);

namespace App\Domains\Group\Action;

use App\Domains\Group\Model\DeviceCvedixrtGroup as Model;
use App\Exceptions\ValidatorException;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    abstract protected function save(): void;

    /**
     * @throws ValidatorException
     */
    public function handle(): void
    {
        $this->data();
        $this->check();
        $this->save();
    }

    protected function data(): void
    {
        $this->dataGroupName();
        $this->dataDescription();
    }

    /**
     * @throws ValidatorException
     */
    protected function check(): void
    {
        $this->existGroupName();
    }

    private function dataGroupName(): void
    {
        $this->data['group_name'] = trim($this->data['group_name']);
    }

    private function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }

    /**
     * @throws ValidatorException
     */
    protected function existGroupName(): void
    {
        if (Model::query()
            ->where('group_name', $this->data['group_name'])
            ->exists()) {
            throw new ValidatorException(__('group-create.error.group_name-exists', ['group_name' => $this->data['group_name']]));
        }
    }
}
