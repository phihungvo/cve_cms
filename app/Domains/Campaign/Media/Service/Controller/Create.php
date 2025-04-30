<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Service\Controller;

use App\Domains\Campaign\Media\Action\Create as CreateAction;
use App\Domains\Campaign\Media\Model\Media;
use App\Domains\Campaign\Model\Campaign;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use getID3;
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
        return $sanitized . '.' . strtolower($extension);
    }

    public function create()
    {
        Log::info('Received create media request', [
            'method' => $this->request->method(),
            'url' => $this->request->url(),
            'headers' => $this->request->headers->all(),
        ]);

        try {
            $campaigns = Campaign::all()->pluck('id')->toArray();

            // Validate request data
            $data = $this->request->validate([
                'media_files' => 'required|array|max:10',
                'media_files.*' => 'file|max:102400|mimes:mp4',
                'campaign_id' => 'nullable|integer|in:' . implode(',', $campaigns),
                'enterprise_id' => $this->auth->hasRole('root') ? 'nullable|integer|exists:enterprise,id' : 'required|integer|exists:enterprise,id',
            ]);

            $files = $this->request->file('media_files');
            $totalSize = 0;
            $createdMedia = [];
            $errors = [];

            // Calculate total file size
            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            if ($totalSize > 1073741824) {
                throw new \Exception('Total file size exceeds 1GB limit.');
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
                Log::error('MinIO connection failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $this->auth->id,
                    'config' => config('filesystems.disks.minio'),
                ]);
                throw new \Exception('Unable to connect to storage server: Invalid credentials or server configuration.');
            }

            $action = new CreateAction();

            // Process each file
            foreach ($files as $index => $file) {
                try {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $this->sanitizeFileName($originalFileName);
                    $mimeType = $file->getMimeType();
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    Log::info('Processing file', [
                        'original' => $originalFileName,
                        'sanitized' => $fileName,
                        'mime' => $mimeType,
                        'extension' => $extension
                    ]);

                    // Validate MP4 file
                    if ($extension !== 'mp4') {
                        throw new \Exception("File $originalFileName is not an MP4 file (invalid extension).");
                    }

                    $getID3 = new getID3();
                    $fileInfo = $getID3->analyze($file->getPathname());
                    if (!isset($fileInfo['fileformat']) || $fileInfo['fileformat'] !== 'mp4') {
                        throw new \Exception("File $originalFileName has .mp4 extension but is not a valid MP4 file.");
                    }

                    // Handle file storage
                    $path = $this->auth->hasRole('root') ? $fileName : "{$enterpriseId}/{$fileName}";
                    $mediaUrl = Storage::disk('minio')->url($path);

                    // Check file existence
                    try {
                        if (Storage::disk('minio')->exists($path) || Media::where('media_url', $mediaUrl)->exists()) {
                            throw new \Exception(__('media-create.media-exists', ['name' => $fileName]));
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to check file existence', [
                            'file' => $originalFileName,
                            'path' => $path,
                            'error' => $e->getMessage(),
                        ]);
                        throw new \Exception("Unable to check existence for: $fileName");
                    }

                    $duration = isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;

                    // Store file
                    try {
                        if ($this->auth->hasRole('root')) {
                            $path = Storage::disk('minio')->putFileAs('', $file, $fileName);
                        } else {
                            $path = Storage::disk('minio')->putFileAs("{$enterpriseId}", $file, $fileName);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to upload file to MinIO', [
                            'file' => $originalFileName,
                            'path' => $path,
                            'error' => $e->getMessage(),
                        ]);
                        throw new \Exception("Failed to upload $fileName to storage.");
                    }

                    if (!Storage::disk('minio')->exists($path)) {
                        throw new \Exception('File not found on MinIO after upload: ' . $fileName);
                    }

                    $mediaUrl = Storage::disk('minio')->url($path);

                    // Prepare media data
                    $mediaData = [
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'file_name' => $fileName,
                        'media_url' => $mediaUrl,
                        'size' => $file->getSize(),
                        'type' => $mimeType,
                        'duration' => $duration,
                        'campaign_id' => $data['campaign_id'] ?? null,
                        'enterprise_id' => $this->auth->hasRole('root') ? null : $enterpriseId,
                    ];

                    // Save media
                    $media = $action->handle($mediaData);
                    $createdMedia[] = $media;

                } catch (\Exception $e) {
                    Log::error('Failed to process file', [
                        'file' => $originalFileName,
                        'error' => $e->getMessage(),
                        'user_id' => $this->auth->id,
                        'role' => $this->auth->hasRole('root') ? 'root' : 'non-root',
                    ]);
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
                    'data' => $createdMedia,
                ];
                return $this->request->ajax() || $this->request->wantsJson()
                    ? response()->json($response, 207)
                    : $createdMedia;
            }

            // Success response
            $response = [
                'success' => true,
                'message' => __('media-create.upload-success', ['count' => count($createdMedia)]),
                'data' => $createdMedia,
            ];

            return $this->request->ajax() || $this->request->wantsJson()
                ? response()->json($response, 200)
                : $createdMedia;

        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'user_id' => $this->auth->id,
            ]);
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