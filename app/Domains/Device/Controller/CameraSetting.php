<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Domains\Device\Service\Controller\UpdateCamera as ControllerService;
use App\Domains\Device\Action\ActionFactory;

class CameraSetting extends ControllerAbstract
{
    /**
     * @param int $id
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(int $id): Response|RedirectResponse
    {
        $this->row($id);

        // Handle POST request for creating camera
        if ($this->request->isMethod('post') && $this->request->route()->named('device.camera-setting.create')) {
            return $this->createCamera();
        }

        // Handle PATCH request for updating cameras
        if ($this->request->isMethod('patch') && $this->request->route()->named('device.update.camera-setting.update')) {
            return $this->updateCameras();
        }

        // Handle DELETE request for deleting camera
        if ($this->request->isMethod('delete') && $this->request->route()->named('device.update.camera-setting.delete')) {
            return $this->deleteCamera();
        }

        $this->meta('title', __('camera.camera-setting-meta-title', ['title' => $this->row->name]));

        return $this->page('device.update-camera-setting', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function updateCameras(): RedirectResponse
    {
        try {
            $this->action()->updateCameras();
            $this->sessionMessage('success', __('camera.update-success'));
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCamera(): RedirectResponse
    {
        try {
            $this->action()->createCamera();
            $this->sessionMessage('success', __('camera.create-success'));
            return redirect()->route('device.update.camera-setting', $this->row->id);
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    protected function deleteCamera(): RedirectResponse
    {
        try {
            $cameraId = (int) $this->request->route('camera_id');
            $this->action()->deleteCamera($cameraId);
            $this->sessionMessage('success', __('camera.delete-success'));
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
        }

        return redirect()->back();
    }
}