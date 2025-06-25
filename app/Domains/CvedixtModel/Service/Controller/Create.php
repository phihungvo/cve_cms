<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Service\Controller;

use App\Domains\CvedixtModel\Action\Create as CreateAction;
use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Sanitize the file name to ensure it is safe for storage.
     *
     * @param string $fileName
     * @return string
     */
    protected function sanitizeFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $sanitized = Str::slug($baseName, '-');

        return $sanitized.'.'.strtolower($extension);
    }

    /**
     * Create a new model with uploaded files.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data = $this->request->validate([
                'model_files' => 'sometimes|array|max:10',
                'parent_id' => 'sometimes|nullable|string',
                'enterprise_id' => $this->auth->hasRole('root') ? 'nullable|integer|exists:enterprise,id' : 'required|integer|exists:enterprise,id',
            ]);

            $files = $this->request->file('model_files') ?? [];
            $parentPath = $data['parent_id'] ?? null;
            $totalSize = 0;
            $createdModel = [];
            $errors = [];

            if (empty($files)) {
                throw new \Exception('No files uploaded.');
            }

            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            if ($totalSize > 2147483648) {
                throw new \Exception('Total file size exceeds 2GB limit.');
            }

            // Validate enterprise ID
            $enterpriseId = $this->auth->hasRole('root') ? ($data['enterprise_id'] ?? null) : ($this->auth->enterprise_id ?? null);
            if (!$this->auth->hasRole('root') && !$enterpriseId) {
                throw new \Exception('User is not associated with any enterprise.');
            }

            // Test MinIO connection
            try {
                $config = config('filesystems.disks.minio');
                $s3Client = new S3Client([
                    'credentials' => ['key' => $config['key'], 'secret' => $config['secret']],
                    'region' => $config['region'],
                    'version' => 'latest',
                    'endpoint' => $config['endpoint'],
                    'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
                ]);
                $s3Client->listBuckets();
            } catch (\Exception $e) {
                throw new \Exception('Unable to connect to storage server: '.$e->getMessage());
            }

            $action = new CreateAction();

            // Prepare the base path for storage
            foreach ($files as $index => $file) {
                try {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $this->sanitizeFileName($originalFileName);
                    $mimeType = $file->getMimeType();

                    $basePath = $parentPath ?: ($enterpriseId ? $enterpriseId : '');
                    $path = $basePath ? "{$basePath}/{$fileName}" : $fileName;
                    $modelUrl = '/'.trim($path, '/');

                    if (Storage::disk('minio')->exists($path) || CvedixtModel::where('model_url', $modelUrl)->exists()) {
                        throw new \Exception(__('File already existed!', ['name' => $fileName]));
                    }

                    $storedPath = Storage::disk('minio')->putFileAs($basePath ?: '', $file, $fileName);
                    if (!Storage::disk('minio')->exists($storedPath)) {
                        throw new \Exception('File not found on MinIO after upload: '.$fileName);
                    }

                    $modelUrl = '/'.trim($storedPath, '/');

                    $modelData = [
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'file_name' => $fileName,
                        'model_url' => $modelUrl,
                        'size' => $file->getSize(),
                        'type' => $mimeType,
                        'enterprise_id' => $enterpriseId,
                        'parent_id' => $parentPath ? CvedixtModel::where('model_url', '/'.trim($parentPath, '/'))->first()?->id : null,
                        'is_folder' => false,
                    ];

                    $media = $action->handle($modelData);
                    $createdModel[] = $media;

                } catch (\Exception $e) {
                    $errors[] = ['file' => $originalFileName, 'error' => $e->getMessage()];
                }
            }

            if (!empty($errors)) {
                $response = [
                    'success' => false,
                    'message' => $errors[0]['error'] ?? 'Some files failed to upload.',
                    'errors' => $errors,
                    'data' => $createdModel,
                ];

                return $this->request->ajax() || $this->request->wantsJson()
                    ? response()->json($response, 207)
                    : $createdModel;
            }

            $response = [
                'success' => true,
                'message' => __('cvedixrt-model.upload_success', ['count' => count($createdModel)]),
                'data' => $createdModel,
            ];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 200)
                : $createdModel;

        } catch (ValidationException $e) {
            $response = ['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 422)
                : throw $e;
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 500)
                : throw $e;
        }
    }
}
