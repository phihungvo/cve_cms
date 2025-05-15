<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Controller;

use Exception;
use App\Domains\CvedixSolution\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('cvedix-solution-create.meta-title'));

        return $this->page('cvedix_solution.create', $this->data());
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

            $this->sessionMessage('success', __('cvedix-solution-create.success'));

            return redirect()->route('cvedix_solution.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedix-solution.create')->withInput();
        }
    }
}
