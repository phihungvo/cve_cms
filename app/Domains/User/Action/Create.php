<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use App\Domains\User\Model\User as Model;

class Create extends CreateUpdateAbstract
{
    /**
     * @return void
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                // Tạo User
                $this->row = Model::query()->create([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'password' => $this->data['password'],
                    'phone' => $this->data['phone'],

                    'api_key_full' => $this->data['api_key_full'],
                    'api_key' => $this->data['api_key'],
                    'api_key_prefix' => $this->data['api_key_prefix'],
                    'api_key_enabled' => $this->data['api_key_enabled'],

                    'preferences' => $this->data['preferences'],

                    'admin' => $this->data['admin'],
                    'admin_mode' => $this->data['admin'],
                    'manager' => $this->data['manager'],
                    'manager_mode' => $this->data['manager'],
                    'enabled' => $this->data['enabled'],

                    'language_id' => $this->data['language_id'],
                    'timezone_id' => $this->data['timezone_id'],
                    'enterprise_id' => auth()->user()->enterprise_id,
                ])->fresh();

                /* Gán Roles cho User */
                if (!empty($this->data['roles'])) {
                    $this->row->roles()->sync(
                        collect($this->data['roles'])->mapWithKeys(fn($roleId) => [
                            $roleId => [
                                'created_at' => now(),
                                'updated_at' => now(),
                                'enterprise_id' => auth()->user()->enterprise_id,
                            ]
                        ])
                    );

                }
            });
        } catch (\Throwable $e) {
            // Roleback tùy chỉnh nếu cần (vd: ghi log lỗi)
            logger()->error('Lỗi khi tạo User: ' . $e->getMessage());
        }
    }
}