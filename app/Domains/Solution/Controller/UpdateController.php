<?php

namespace App\Domains\Solution\Controller;

use App\Domains\Solution\Service\Controller\UpdateService as ServiceController;
use App\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;

class UpdateController extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        try {
            $this->row($id);
            $this->device((int)$this->request->query('deviceId'));
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('solution.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('solution-update.meta-title'));

        return $this->page('solution.update', $this->data());
    }

    protected function data()
    {
        return ServiceController::new($this->request, $this->auth, $this->row, $this->device)->data();
    }

    protected function actions()
    {
        return $this->actionPost('update')
            ?: $this->actionPost('delete');
    }

    public function update(): RedirectResponse
    {
        try {
            $this->action()->update();
            $this->sessionMessage('success', __('solution-update.success'));
            return redirect()->route('solution.index', ['deviceId' => $this->device->id]);
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('solution.update', ['deviceId' => $this->device->id])->withInput();
        }
    }

    public function delete(): RedirectResponse
    {
        try {
            $this->action()->delete();
            $this->sessionMessage('success', __('solution-update.delete-success'));
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
        } finally {
            return redirect()->route('solution.index', ['deviceId' => $this->device->id]);
        }
    }

}
