<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Controller;

use Exception;
use App\Domains\VehicleGroup\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('vehicle-group-create.meta-title'));

        return $this->page('vehicle-group.create', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    /**
     * @return RedirectResponse
     */
    protected function create(): RedirectResponse
    {
        try {
            $this->row = $this->action()->create();

            $this->sessionMessage('success', __('vehicle-group-create.success'));

            return redirect()->route('vehicle_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('vehicle_group.create')->withInput();
        }
    }
}
