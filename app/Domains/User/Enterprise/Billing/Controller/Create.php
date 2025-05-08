<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

use App\Domains\User\Enterprise\Billing\Service\Controller\Create as CreateBilling;

class Create extends ControllerWebAbstract
{
    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('billing.meta-title-create'));
        return $this->page('user.enterprise.billing.create', $this->data());
    }

    public function data(): array
    {
        return CreateBilling::new($this->request, $this->auth)->data();
    }

    protected function create(): RedirectResponse
    {
        $service = CreateBilling::new($this->request, $this->auth);
        try {
            $service->create();
            $this->sessionMessage('success', __('billing.success-create'));
            return redirect()->route('user.enterprise.billing.index', $this->data());
        } catch (ValidationException $e) {
            $this->sessionMessage('error', $e->errors()['message'][0] ?? 'An error occurred while creating the billing.');
            return redirect()->back()->withInput()->withErrors($e->errors());
        }
    }
}