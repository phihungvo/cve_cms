<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Controller;

use Exception;
use App\Domains\CvedixrtInstance\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('cvedixrt-instance-create.meta-title'));

        return $this->page('cvedixrt_instance.create', $this->data());
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

            $this->sessionMessage('success', __('cvedixrt-instance-create.success'));

            return redirect()->route('cvedixrt_instance.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedixrt-instance.create')->withInput();
        }
    }
}
