<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

use App\Domains\User\Enterprise\EService\Service\Controller\Create as CreateService;

class Create extends ControllerWebAbstract
{
    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('eservice-create.meta-title'));
        return $this->page('user.enterprise.eservice.create', $this->data());
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
            $this->sessionMessage('success', __('eservice-create.success'));
            return redirect()->route('user.enterprise.eservice.index', $this->data());
        } catch (ValidationException $e) {
            $this->sessionMessage('error', $e->errors()['message'][0] ?? 'An error occurred while creating the eservice.');
            return redirect()->back()->withInput()->withErrors($e->errors());
        }
    }
}