<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use Illuminate\Support\Facades\Hash;
use App\Domains\User\Exception\AuthFailed;
use App\Domains\User\Model\User as Model;
use Illuminate\Support\Facades\Log;

class AuthCredentials extends ActionAbstract
{
    public function handle(): Model
    {
        $this->data();
        $this->checkIp();
        $this->row();
        $this->check();
        $this->save();
        return $this->row;
    }

    protected function data(): void
    {
        $this->data['email'] = $this->request->input('email');
        $this->data['password'] = $this->request->input('password');
    }

    protected function checkIp(): void
    {
        try {
            $this->factory('IpLock')->action()->check();
            Log::info('IP check passed for: ' . $this->request->ip()); // Sử dụng Log:: thay vì \Log::
        } catch (\Exception $e) {
            Log::error('IP check failed: ' . $e->getMessage()); // Sử dụng Log::
            throw $e;
        }
    }

    protected function row(): void
    {
        $this->row = Model::query()
            ->with(['roles'])
            ->where(function ($query) {
                $query->byEmail($this->data['email'])
                    ->orWhere(function ($q) {
                        $q->byPhone($this->data['email']);
                    });
            })
            ->enabled()
            ->firstOr(fn() => $this->fail());
    }

    protected function check(): void
    {
        $this->checkPassword();
    }

    protected function checkPassword(): void
    {
        if (Hash::check($this->data['password'], $this->row->password) === false) {
            Log::error('Password mismatch for email/phone: ' . $this->data['email']); // Sử dụng Log::
            $this->fail();
        }
    }

    protected function fail(): void
    {
        $this->factory('UserFail')->action($this->failData())->create();
        throw new AuthFailed(__('user-auth-credentials.error.auth-fail'));
    }

    protected function failData(): array
    {
        return [
            'type' => 'user-auth-credentials',
            'text' => $this->data['email'],
            'ip' => $this->request->ip(),
            'user_id' => $this->row?->id,
        ];
    }

    protected function save(): void
    {
        $this->saveSet();
        $this->saveAuth();
        $this->saveUserSession();
    }

    protected function saveSet(): void
    {
        $this->factory()->action()->set();
    }

    protected function saveAuth(): void
    {
        $this->auth = $this->row;
    }

    protected function saveUserSession(): void
    {
        $this->factory('UserSession')->action($this->saveUserSessionData())->create();
    }

    protected function saveUserSessionData(): array
    {
        return [
            'auth' => $this->row->email ?? $this->row->phone,
            'ip' => $this->request->ip(),
            'user_id' => $this->row->id,
        ];
    }
}
