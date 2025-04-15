<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\User\Role\Feature\Service\Controller\Create as CreateService;
use App\Domains\User\Role\Feature\Model\Feature as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Create extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('role-feature-create.meta-title'));
        return $this->page('user.role.feature.create', $this->data());
    }

    protected function data(): array
    {
        return CreateService::new($this->request, $this->auth)->data();
    }

    protected function create(): RedirectResponse
    {
        $service = CreateService::new($this->request, $this->auth);
        $this->row = $service->create();
        $this->sessionMessage('success', __('role-feature-create.success'));
        return redirect()->route('user.role.feature.index', $this->data());
    }
}