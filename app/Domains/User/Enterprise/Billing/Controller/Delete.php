<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\Billing\Service\Controller\Delete as DeleteBilling;
use App\Domains\User\Enterprise\Billing\Model\Billing as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Delete extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            // Tìm bản ghi, bao gồm cả bản ghi đã bị soft delete
            $this->row = Model::withTrashed()->find($id);
            if (!$this->row) {

                $this->sessionMessage('error', __('license-delete.not-found'));
                return redirect()->back();
            }

            $service = DeleteBilling::new($this->request, $this->auth);

            // Gọi delete, logic soft delete/force delete được xử lý trong Action
            $service->delete($this->row);

            $messageKey = $this->row->trashed() ? 'billing.force-delete-success' : 'billing.delete-success';
            $this->sessionMessage('success', __($messageKey));

            return redirect()->route('user.enterprise.billing.index');
        } catch (\Exception $e) {
            Log::error('Controller: license deletion failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->sessionMessage('error', __('billing.delete-error'));
            return redirect()->back();
        }
    }
}