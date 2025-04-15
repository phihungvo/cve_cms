<?php declare(strict_types=1);

namespace App\Domains\User\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Services\Captcha\Captcha;

class AuthCredentials extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(): Response|RedirectResponse
    {
        if ($response = $this->actionPost('authCredentials')) {
            return $response;
        }

        $this->meta('title', __('user-auth-credentials.meta-title'));

        return $this->page('user.auth-credentials', [
            'captcha' => Captcha::new()->requiredAuth(),
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authCredentials(): RedirectResponse
    {
        $this->request->validate([
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9]{8,15}$/', $value)) {
                        $fail(__('user-auth-credentials.error.email-or-phone-invalid'));
                    }
                }
            ],
            'password' => 'required|string',
        ]);

        // Truyền dữ liệu đã validate vào action
        $this->action()->authCredentials();

        return redirect()->route('dashboard.index');
    }
}
