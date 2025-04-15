<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Camera;

class DeleteCamera extends ActionAbstract
{
    /**
     * @return void
     */
    public function handle(): void
    {
        $cameraId = (int) $this->request->route('camera_id');
        $this->delete($cameraId);
    }

    /**
     * @param int $cameraId
     * @return void
     */
    protected function delete(int $cameraId): void
    {
        $camera = Camera::find($cameraId);

        if (!$camera) {
            throw new \Exception(__('camera.error.not_found', ['id' => $cameraId]));
        }

        if ($camera->device_id !== $this->row->id) {
            throw new \Exception(__('camera.error.unauthorized'));
        }

        $camera->delete();
    }
}