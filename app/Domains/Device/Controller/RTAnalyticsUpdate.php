<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;


use App\Domains\Device\Service\Controller\RTAnalyticsUpdate as ServiceController;
use App\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;

class RTAnalyticsUpdate extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        try {
            $this->row($id);
            $instanceId = (int)$this->request->query('instanceId');
            $this->deviceCvedixInstance($instanceId);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.runtime-analytics');
        }
        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('device-create.meta-title-analytics'));

        return $this->page('device.rt-analytics-update', $this->data());
    }

    protected function data(): array
    {
        return ServiceController::new($this->request, $this->auth, $this->row, $this->deviceCveditInstance)->data();
    }

    protected function actions()
    {
        return $this->actionPost('update')
            ?: $this->actionPost('delete');
    }

    protected function update(): RedirectResponse
    {
        try {
            $this->action()->updateInstance($this->deviceCveditInstance);
            $this->sessionMessage('success', __('Update Instance Success'));
            return redirect()->route('device.runtime-analytics', $this->row->id);
        }catch (\Exception $e){
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.runtime-analytics', $this->row->id);
        }
    }

    protected function delete(): RedirectResponse
    {
        try {
            $this->action()->deleteInstance();
            $this->sessionMessage('success', __('Delete Instance Success'));
            return redirect()->route('device.runtime-analytics', $this->row->id);
        }catch (\Exception $e){
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.runtime-analytics', $this->row->id);
        }
    }
}
