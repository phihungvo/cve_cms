<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use Illuminate\Http\RedirectResponse;
use App\Domains\User\Enterprise\EService\Service\Controller\Delete as DeleteService;
use App\Domains\User\Enterprise\EService\Model\EService as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Delete extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): RedirectResponse
    {
        $this->row = Model::findOrFail($id);
        $service = DeleteService::new($this->request, $this->auth);
        $service->delete($this->row);
        $this->sessionMessage('success', __('permission-update.delete-success'));
        return redirect()->route('user.permission.index');
    }
}