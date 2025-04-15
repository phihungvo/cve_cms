<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Camera;

class UpdateCameras extends ActionAbstract
{
    /**
     * @return void
     */
    public function handle(): void
    {
        $this->update();
    }

    /**
     * @return void
     */
    protected function update(): void
    {
        $camerasData = $this->request->input('cameras', []);
        \Illuminate\Support\Facades\Log::info('Cameras data received:', $camerasData);

        if (empty($camerasData)) {
            throw new \Exception('No camera data received.');
        }

        foreach ($camerasData as $cameraId => $data) {
            $camera = Camera::find($cameraId);

            if (!$camera) {
                throw new \Exception(__('camera.error.not_found', ['id' => $cameraId]));
            }

            if ($camera->device_id !== $this->row->id) {
                throw new \Exception(__('camera.error.unauthorized'));
            }

            $camera->update([
                'name' => $data['name'] ?? $camera->name,
                'description' => $data['description'] ?? $camera->description,
                'model' => $data['model'] ?? $camera->model,
                'serial' => $data['serial'] ?? $camera->serial,
                'uri' => $data['uri'] ?? $camera->uri,
                'location' => $data['location'] ?? $camera->location,
                'resolution' => $data['resolution'] ?? $camera->resolution,
            ]);
        }
    }
}