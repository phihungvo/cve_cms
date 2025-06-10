<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RTAnalyticsCreate as ServiceController;
use App\Domains\Device\Service\Controller\RTAnalyticsInputSource as ServiceControllerInputSource;
use App\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsCreate extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.runtime-analytics');
        }

        if ($response = $this->actionPost('next')) {
            return $response;
        }

        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('rt-analytics-create.meta-title-create'));

        return $this->page('device.rt-analytics-create', $this->data());
    }

    protected function data(): array
    {
        return ServiceController::new($this->request, $this->auth, $this->row)->data();
    }

    public function create(): RedirectResponse
    {
        try {
            $stepData = $this->request->session()->get('stepData', []);
            $finalData = array_merge($stepData, $this->request->except('_token', '_action'));

            $this->request->merge($finalData);

            $this->instance = $this->action()->createInstance();

            $this->request->session()->forget('stepData');

            $this->sessionMessage('success', __('rt-analytics-create.create-success'));

            return redirect()->route(
                'device.runtime-analytics.analytcs-rules',
                ['id' => $this->row->id, 'instanceId' => $this->instance->id]
            );
        } catch (\Exception $exception) {
            $this->sessionMessage('error', $exception->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function next(): Response|RedirectResponse
    {
        try {
            $stepData = $this->request->except('_token', '_action');
            $this->request->session()->put('stepData', $stepData);

            return $this->page('device.rt-analytics-input-source', $this->dataInputSource());
        } catch (\Exception $exception) {
            $this->sessionMessage('error', $exception->getMessage());

            return redirect()->back()->withInput();
        }

    }

    protected function dataInputSource(): array
    {
        return ServiceControllerInputSource::new($this->request, $this->auth, $this->row)->data();
    }
}
