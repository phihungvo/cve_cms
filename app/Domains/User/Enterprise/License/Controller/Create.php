<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

use App\Domains\User\Enterprise\License\Service\Controller\Create as CreateLicense;

class Create extends ControllerWebAbstract
{
    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('eservice-create.meta-title'));
        return $this->page('user.enterprise.license.create', $this->data());
    }

    public function data(): array
    {
        return CreateLicense::new($this->request, $this->auth)->data();
    }

    protected function create(): RedirectResponse
    {
        $service = CreateLicense::new($this->request, $this->auth);
        try {
            $service->create();
            $this->sessionMessage('success', __('license-create.success'));
            return redirect()->route('user.enterprise.license.index', $this->data());
        } catch (ValidationException $e) {
            $this->sessionMessage('error', $e->errors()['message'][0] ?? 'An error occurred while creating the license.');
            return redirect()->back()->withInput()->withErrors($e->errors());
        }
    }
}