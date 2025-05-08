<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\Billing\Service\Controller\Update as UpdateBilling;
use App\Domains\User\Enterprise\Billing\Model\Billing as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Update extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): Response|RedirectResponse
    {
        Log::info('UpdateController: Starting request', ['id' => $id]);

        try {
            $this->row = Model::withTrashed()->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('UpdateController: License not found', ['id' => $id]);
            $this->sessionMessage('error', __('license-update.not-found'));
            return redirect()->route('user.enterprise.license.index');
        }

        if ($this->request->isMethod('patch')) {
            return $this->update();
        }

        $this->meta('title', __('license-update.meta-title'));
        return $this->page('user.enterprise.license.update', $this->data());
    }

    protected function data(): array
    {
        $data = array_merge(
            ['row' => $this->row],
            ['can_be_deleted' => $this->canBeDeleted()],
            UpdateLicense::new($this->request, $this->auth)->data(),
        );

        return $data;
    }

    protected function update(): RedirectResponse
    {
        Log::info('UpdateController: Starting update', ['id' => $this->row->id]);
        try {
            $service = UpdateLicense::new($this->request, $this->auth);
            $this->row = $service->update($this->row);

            $this->sessionMessage('success', __('license-update.success'));
            return redirect()->route('user.enterprise.license.index');
        } catch (\Exception $e) {
            Log::error('UpdateController: Update failed', [
                'id' => $this->row->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->sessionMessage('error', __('license-update.error'));
            return redirect()->back();
        }
    }

    protected function canBeDeleted(): bool
    {
        return true; // Điều kiện xóa tùy logic của bạn
    }
}