<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\User\Role\Feature\Service\Controller\Update as UpdateService;
use App\Domains\User\Role\Feature\Model\Feature as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Update extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): Response|RedirectResponse
    {
        $this->row = Model::findOrFail($id);

        if ($this->request->isMethod('patch')) {
            return $this->update();
        }

        $this->meta('title', __('role-feature-update.meta-title'));
        return $this->page('user.role.feature.update', $this->data()); // Đảm bảo tên view đúng
    }

    protected function data(): array
    {
        return array_merge(
            ['row' => $this->row],
            ['can_be_deleted' => $this->canBeDeleted()],
            UpdateService::new($this->request, $this->auth)->data()
        );
    }

    protected function update(): RedirectResponse
    {
        $service = UpdateService::new($this->request, $this->auth);
        $this->row = $service->update($this->row);
        $this->sessionMessage('success', __('role-feature-update.success'));
        return redirect()->route('user.role.feature.index');
    }

    protected function canBeDeleted(): bool
    {
        return true; // Điều kiện xóa tùy logic của bạn
    }
}