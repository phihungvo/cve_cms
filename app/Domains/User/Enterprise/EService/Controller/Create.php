<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

use App\Domains\User\Permission\Service\Controller\Create as CreateService;

class Create extends ControllerWebAbstract
{
    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('permission-create.meta-title'));
        return $this->page('user.permission.create', $this->data());
    }

    public function data(): array
    {
        return CreateService::new($this->request, $this->auth)->data();
    }

    protected function create(): RedirectResponse
    {
        $service = CreateService::new($this->request, $this->auth);
        try {
            $service->create();
            $this->sessionMessage('success', __('permission-create.success'));
            return redirect()->route('user.permission.index', $this->data());
        } catch (ValidationException $e) {
            $this->sessionMessage('error', $e->errors()['message'][0] ?? 'An error occurred while creating the permission.');
            return redirect()->back()->withInput()->withErrors($e->errors());
        }
    }
}