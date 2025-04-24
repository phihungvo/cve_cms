<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\EService\Service\Controller\Delete as DeleteService;
use App\Domains\User\Enterprise\EService\Model\EService as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Delete extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): Response|RedirectResponse
    {
        Log::info('Controller: Starting EService deletion', ['id' => $id]);

        try {
            $this->row = Model::findOrFail($id);
            Log::info('Controller: EService found', ['id' => $this->row->id, 'deleted_at' => $this->row->deleted_at]);

            $service = DeleteService::new($this->request, $this->auth);
            Log::info('Controller: DeleteService instantiated');

            $service->delete($this->row);
            Log::info('Controller: EService deleted successfully');

            $this->sessionMessage('success', __('eservice-update.delete-success'));
            return redirect()->route('user.enterprise.eservice.index');
        } catch (\Exception $e) {
            Log::error('Controller: EService deletion failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->sessionMessage('error', __('eservice-update.delete-error'));
            return redirect()->back();
        }
    }
}