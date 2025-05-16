<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Controller;

use Exception;
use App\Domains\CvedixrtInstance\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class CreateController extends ControllerAbstract
{
    public function __invoke(): Response|RedirectResponse
    {

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('cvedixrt-instance-create.meta-title'));

        return $this->page('cvedixrt-instance.create', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    protected function actions(): RedirectResponse|Response|false|null
    {
        return $this->actionPost('next')
            ?: $this->actionPost('create');
    }

    protected function dataInputSource(): array
    {
        return ControllerService::new($this->request, $this->auth)->dataInputSource();
    }

    protected function next(): Response|RedirectResponse
    {
        try {
            $stepData = $this->request->except('_token', '_action');
            $this->request->session()->put('stepData', $stepData);

            return $this->page('cvedixrt-instance.create-next', $this->dataInputSource());
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * @return RedirectResponse
     */
    protected function create(): RedirectResponse
    {
        try {
            $stepData = $this->request->session()->get('stepData', []);

            $finalData = array_merge($stepData, $this->request->except('_token', '_action'));

            $this->request->merge($finalData);

            $this->row = $this->action()->create();

            $this->request->session()->forget('stepData');

            $this->sessionMessage('success', __('cvedixrt-instance-create.success'));

            return redirect()->route('cvedixrt_instance.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedixrt_instance.create')->withInput();
        }
    }
}
