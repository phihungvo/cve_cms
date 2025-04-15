<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Camera;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateCamera extends ActionAbstract
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function handle(): void
    {
        $this->check();
        $this->validate();
        $this->checkExisting();
        $this->create();
    }

    /**
     * @return void
     */
    protected function check(): void
    {
        // Check if camera_supported is enabled
        if (!$this->row->camera_supported) {
            $this->exceptionValidator(__('camera.not_supported'));
        }

        // Check if the number of cameras exceeds camera_maximum
        $currentCameraCount = $this->row->cameras()->count();
        if ($currentCameraCount >= $this->row->camera_maximum) {
            $this->exceptionValidator(__('camera.error.exceeds_maximum', ['maximum' => $this->row->camera_maximum]));
        }
    }

    /**
     * @return void
     */
    protected function validate(): void
    {
        $data = $this->request->input('camera', []);

        // Define validation rules
        $rules = [
            'camera.name' => 'required|string|max:255',
            'camera.uri' => 'required|url',
            'camera.serial' => 'required|string|max:255',
        ];

        // Define custom error messages
        $messages = [
            'camera.name.required' => __('camera.error.name_required'),
            'camera.uri.url' => __('camera.error.invalid_uri'),
            'camera.uri.required' => __('camera.error.uri_required'),
            'camera.serial.required' => __('camera.error.serial_required'),
        ];

        // Validate the data
        $validator = Validator::make($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    protected function checkExisting(): void
    {
        $data = $this->request->input('camera', []);

        // Check if a camera with the same name or serial already exists for this device
        $existingCamera = Camera::where('device_id', $this->row->id)
            ->where(function ($query) use ($data) {
                $query->where('name', $data['name']);
            })
            ->first();

        if ($existingCamera) {
            throw new \Exception(__('camera.error.already_exists', ['name' => $data['name']]));
        }

        // Check if a camera with the same serial already exists for this device
        $existingSerial = Camera::where('serial', $data['serial'])->exists();

        if ($existingSerial) {
            throw new \Exception(__('camera.error.serial_exists', ['serial' => $data['serial']]));
        }
    }

    /**
     * @return void
     */
    protected function create(): void
    {
        $data = $this->request->input('camera', []);

        Log::info('Creating camera with data: ', $data);

        try {
            $camera = Camera::create([
                'name' => $data['name'],
                'device_id' => $this->row->id,
                'description' => $data['description'] ?? '',
                'model' => $data['model'] ?? null,
                'serial' => $data['serial'] ?? null,
                'uri' => $data['uri'] ?? null,
                'location' => $data['location'] ?? null,
                'resolution' => $data['resolution'] ?? null,
            ]);

            //            if ($camera) {
            //                Log::info('Camera created successfully: ', $camera->toArray());
            //            } else {
            //                Log::error('Failed to create camera: ', $data);
            //                throw new \Exception('Failed to create camera record in database.');
            //            }
        } catch (\Exception $e) {
            // Log::error('Error creating camera: ', ['error' => $e->getMessage()]);
            throw new \Exception(__('camera.error.creation_failed', ['message' => $e->getMessage()]));
        }
    }
}
