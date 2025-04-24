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
            $this->deviceCvedixrtInstance($instanceId);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            $route = isset($instanceId) ? 'device.runtime-analytics' : 'device.index';

            return redirect()->route($route, ['id' => $this->row->id ?? null]);
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('rt-analytics-create.meta-title-update'));

        return $this->page('device.rt-analytics-update', $this->data());
    }

    protected function data(): array
    {
        return ServiceController::new($this->request, $this->auth, $this->row, $this->deviceCvedixrtInstance)->data();
    }

    protected function actions()
    {
        return $this->actionPost('update')
            ?: $this->actionPost('delete');
    }

    /**
     * Update the instance.
     *
     * @return RedirectResponse
     */
    protected function update(): RedirectResponse
    {
        try {
            $this->action()->updateInstance($this->deviceCvedixrtInstance);
            $this->sessionMessage('success', __(__('rt-analytics-update.update-success')));

            return redirect()->route('device.runtime-analytics', $this->row->id);
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.runtime-analytics', $this->row->id);
        }
    }

    /**
     * Hard Delete the instance.
     *
     * @return RedirectResponse
     */
    protected function delete(): RedirectResponse
    {
        try {
            $this->action()->deleteInstance();
            $this->sessionMessage('success', __('Delete Instance Success'));

            return redirect()->route('device.runtime-analytics', $this->row->id);
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.runtime-analytics', $this->row->id);
        }
    }
}
