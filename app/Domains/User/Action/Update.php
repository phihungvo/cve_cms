<?php declare(strict_types=1);

namespace App\Domains\User\Action;

class Update extends CreateUpdateAbstract
{
    /**
     * @return void
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                $this->row->name = $this->data['name'];
                $this->row->email = $this->data['email'];
                $this->row->password = $this->data['password'];
                $this->row->phone = $this->data['phone'];

                $this->row->api_key_full = $this->data['api_key_full'];
                $this->row->api_key = $this->data['api_key'];
                $this->row->api_key_prefix = $this->data['api_key_prefix'];
                $this->row->api_key_enabled = $this->data['api_key_enabled'];

                $this->row->preferences = $this->data['preferences'];

                $this->row->admin = $this->data['admin'];
                $this->row->admin_mode = $this->data['admin'];
                $this->row->manager = $this->data['manager'];
                $this->row->manager_mode = $this->data['manager'];
                $this->row->enabled = $this->data['enabled'];

                $this->row->language_id = $this->data['language_id'];
                $this->row->timezone_id = $this->data['timezone_id'];

                $this->row->save();
                if (!isset($this->data['roles']) || empty($this->data['roles'])) {
                    $this->row->roles()->sync([]);
                } else {
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
            logger()->error('Lỗi khi cập nhật User: ' . $e->getMessage());
        }

    }
}
