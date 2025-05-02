<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\License\Service\Controller\Update as UpdateLicense;
use App\Domains\User\Enterprise\License\Model\License as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Update extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): Response|RedirectResponse
    {
        Log::info('UpdateController: Starting request', ['id' => $id]);

        try {
            $this->row = Model::withTrashed()->findOrFail($id);
            Log::info('UpdateController: EService found', [
                'id' => $this->row->id,
                'deleted_at' => $this->row->deleted_at
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('UpdateController: EService not found', ['id' => $id]);
            $this->sessionMessage('error', __('eservice-update.not-found'));
            return redirect()->route('user.enterprise.eservice.index');
        }

        if ($this->request->isMethod('patch')) {
            return $this->update();
        }

        $this->meta('title', __('eservice-update.meta-title'));
        return $this->page('user.enterprise.eservice.update', $this->data());
    }

    protected function data(): array
    {
        Log::info('UpdateController: Preparing data', ['id' => $this->row->id]);
        $data = array_merge(
            ['row' => $this->row],
            ['can_be_deleted' => $this->canBeDeleted()],
            UpdateLicense::new($this->request, $this->auth)->data(),
        );
        Log::info('UpdateController: Data prepared', ['can_be_deleted' => $data['can_be_deleted']]);
        return $data;
    }

    protected function update(): RedirectResponse
    {
        Log::info('UpdateController: Starting update', ['id' => $this->row->id]);
        try {
            $service = UpdateLicense::new($this->request, $this->auth);
            $this->row = $service->update($this->row);
            Log::info('UpdateController: EService updated', ['id' => $this->row->id]);
            $this->sessionMessage('success', __('eservice-update.success'));
            return redirect()->route('user.enterprise.eservice.index');
        } catch (\Exception $e) {
            Log::error('UpdateController: Update failed', [
                'id' => $this->row->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->sessionMessage('error', __('eservice-update.error'));
            return redirect()->back();
        }
    }

    protected function canBeDeleted(): bool
    {
        return true; // Điều kiện xóa tùy logic của bạn
    }
}