<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use Illuminate\Support\Facades\Auth;

class Logout extends ActionAbstract
{
    /**
     * @return void
     */
    public function handle(): void
    {
        $this->logout();
        $this->session();
    }

    /**
     * @return void
     */
    protected function logout(): void
    {

        $user = Auth::user();
        $userId = $user ? $user->id : null;
        session()->forget('userPermission_' . $userId);
        session()->forget('userEnterprise_' . $userId);

        Auth::logout();
        session()->flush();
    }

    /**
     * @return void
     */
    protected function session(): void
    {
        $this->request->session()->flush();
    }
}
