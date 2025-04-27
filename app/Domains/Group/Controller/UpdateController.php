<?php

namespace App\Domains\Group\Controller;

use App\Domains\Group\Service\Controller\UpdateService as ServiceController;
use App\Exceptions\NotFoundException;
use Exception;
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
            return redirect()->route('group.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('group-update.meta-title'));

        return $this->page('group.update', $this->data());
    }

    protected function data(): array
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
            $this->sessionMessage('success', __('group-update.success'));
            return redirect()->route('group.index',['deviceId' => $this->device->id]);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete(): RedirectResponse
    {
        try {
            $this->action()->delete();
            $this->sessionMessage('success', __('group-update.delete-success'));
        }catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
        }finally {
            return redirect()->route('group.index',['deviceId' => $this->device->id]);
        }

    }
}
