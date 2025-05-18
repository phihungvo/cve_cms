<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Controller;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\Vehicle\Service\Controller\Create as ControllerService;

class Create extends ControllerAbstract
{
    /**
     * @return Response|RedirectResponse
     */
    public function __invoke(): Response|RedirectResponse
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('vehicle-create.meta-title'));

        return $this->page('vehicle.create', $this->data());
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

            $this->sessionMessage('success', __('vehicle-create.success'));

            return redirect()->route('vehicle.update', $this->row->id);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }
}
