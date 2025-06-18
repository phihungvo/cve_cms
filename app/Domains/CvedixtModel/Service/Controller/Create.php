<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Service\Controller;

use App\Domains\CvedixtModel\Action\Create as CreateAction;
use App\Domains\CvedixtModel\Model\CvedixtModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Aws\S3\S3Client;

class Create
{
    protected $request;

    protected $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {
        return [];
    }

    protected function sanitizeFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $sanitized = Str::slug($baseName, '-');

        return $sanitized.'.'.strtolower($extension);
    }

    public function create()
    {
        try {
            // Validate request data
            $data = $this->request->validate([
                'model_files' => 'required|array|max:10',
                'media_files.*' => 'file|max:102400|mimes:mp4',
//                'model_files.*' => 'file|mimes:pt,onnx,pth,h5,tflite,joblib,pkl,sav,ckpt',
                'enterprise_id' => $this->auth->hasRole('root') ? 'nullable|integer|exists:enterprise,id' : 'required|integer|exists:enterprise,id',
            ]);

            $files = $this->request->file('model_files');
            $totalSize = 0;
            $createdModel = [];
            $errors = [];

            // Calculate total file size
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            if ($totalSize > 2147483648) {
                throw new \Exception('Total file size exceeds 2GB limit.');
            }

            // Handle enterprise_id
            $enterpriseId = $this->auth->hasRole('root') ? ($data['enterprise_id'] ?? null) : ($this->auth->enterprise_id ?? null);
            if (!$this->auth->hasRole('root') && !$enterpriseId) {
                throw new \Exception('User is not associated with any enterprise.');
            }

            // Test MinIO connection
            try {
                $config = config('filesystems.disks.minio');
                $s3Client = new S3Client([
                    'credentials' => [
                        'key' => $config['key'],
                        'secret' => $config['secret'],
                    ],
                    'region' => $config['region'],
                    'version' => 'latest',
                    'endpoint' => $config['endpoint'],
                    'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
                ]);
                $s3Client->listBuckets();
            } catch (\Exception $e) {
                throw new \Exception('Unable to connect to storage server: Invalid credentials or server configuration.');
            }

            $action = new CreateAction();

            // Process each file
            foreach ($files as $index => $file) {
                try {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $this->sanitizeFileName($originalFileName);
                    $mimeType = $file->getMimeType();

                    // Handle file storage
                    $path = $this->auth->hasRole('root') ? $fileName : "{$enterpriseId}/{$fileName}";
                    //                    $modelUrl = Storage::disk('minio')->url($path);
                    $modelUrl = '/'.$path;

                    // Check file existence
                    try {
                        if (Storage::disk('minio')->exists($path) || CvedixtModel::where('model_url', $modelUrl)->exists()) {
                            throw new \Exception(__('model-create.model-exists', ['name' => $fileName]));
                        }
                    } catch (\Exception $e) {
                        throw new \Exception("Unable to check existence for: $fileName");
                    }

                    // Store file
                    try {
                        if ($this->auth->hasRole('root')) {
                            $path = Storage::disk('minio')->putFileAs('', $file, $fileName);
                        } else {
                            $path = Storage::disk('minio')->putFileAs("{$enterpriseId}", $file, $fileName);
                        }
                    } catch (\Exception $e) {
                        throw new \Exception("Failed to upload $fileName to storage.");
                    }

                    if (!Storage::disk('minio')->exists($path)) {
                        throw new \Exception('File not found on MinIO after upload: '.$fileName);
                    }

                    //                    $modelUrl = Storage::disk('minio')->url($path);
                    $modelUrl = '/'.$path;

                    // Prepare media data
                    $modelData = [
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'file_name' => $fileName,
                        'model_url' => $modelUrl,
                        'size' => $file->getSize(),
                        'type' => $mimeType,
                        'enterprise_id' => $this->auth->hasRole('root') ? null : $enterpriseId,
                    ];

                    // Save media
                    $media = $action->handle($modelData);
                    $createdModel[] = $media;

                } catch (\Exception $e) {
                    $errors[] = [
                        'file' => $originalFileName,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Handle partial success or errors
            if (!empty($errors)) {
                $response = [
                    'success' => false,
                    'message' => 'Some files failed to upload.',
                    'errors' => $errors,
                    'data' => $createdModel,
                ];

                return $this->request->ajax() || $this->request->wantsJson()
                    ? response()->json($response, 207)
                    : $createdModel;
            }

            // Success response
            $response = [
                'success' => true,
                'message' => __('model-create.upload-success', ['count' => count($createdModel)]),
                'data' => $createdModel,
            ];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 200)
                : $createdModel;

        } catch (ValidationException $e) {
            $response = [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 422)
                : throw $e;

        } catch (\Exception $e) {
            Log::error('Failed to process request', [
                'error' => $e->getMessage(),
                'user_id' => $this->auth->id,
                'role' => $this->auth->hasRole('root') ? 'root' : 'non-root',
            ]);
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 500)
                : throw $e;
        }
    }
}
