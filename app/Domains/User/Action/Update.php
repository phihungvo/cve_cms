<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use RuntimeException;
use Throwable;

class Update extends CreateUpdateAbstract
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
                $this->row->update($this->data);

                // Đồng bộ roles
                if ($this->data['roles'] !== null) {
                    $this->row->roles()->sync(
                        collect($this->data['roles'])->mapWithKeys(fn ($roleId) => [
                            $roleId => ['enterprise_id' => $this->row->enterprise_id],
                        ])
                    );
                }

                // Đồng bộ groups
                $this->row->groups()->sync($this->data['groups']);
            });
        } catch (Throwable $e) {
            logger()->error('Lỗi khi cập nhật User: '.$e->getMessage());
            throw new RuntimeException('Error updating User: '.$e->getMessage(), 0, $e);
        }
    }

    protected function dataRoleIds(): void
    {
        if ($this->auth->enterprise_id == $this->row->enterprise_id) {
            # co quyen sua
            if ($this->request->input('roles')) {
                $this->data['roles'] = $this->request->input('roles');
            } else {
                $this->data['roles'] = [];
            }
        } else {
            # khong co quyen sua
            $this->data['roles'] = null;
        }
    }
}
