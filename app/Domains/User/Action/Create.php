<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use App\Domains\User\Model\User as Model;
use RuntimeException;
use Throwable;

class Create extends CreateUpdateAbstract
{
    /**
     * @throws Throwable
     *
     * @return void
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                // Tạo User
                $this->row = Model::query()->create(array_merge(
                    $this->data,
                    ['enterprise_id' => auth()->user()->enterprise_id]
                ))->fresh();

                // Đồng bộ roles
                $this->row->roles()->sync(
                    collect($this->data['roles'])->mapWithKeys(fn ($roleId) => [
                        $roleId => [
                            'enterprise_id' => auth()->user()->enterprise_id,
                        ],
                    ])
                );

                // Đồng bộ groups
                $this->row->groups()->sync($this->data['groups']);
            });
        } catch (Throwable $e) {
            logger()->error('Lỗi khi tạo User: '.$e->getMessage());
            throw new RuntimeException('Error creating User: '.$e->getMessage(), 0, $e);
        }
    }

    protected function dataRoleIds(): void
    {
        $this->data['roles'] = $this->request->input('roles');
    }
}
