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
use getID3;

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
        try {
            $campaigns = Campaign::all()->pluck('id')->toArray();

            $data = $this->request->validate([
                'name' => 'nullable|string|max:255',
                'media_files' => 'required|array|max:10',
                'media_files.*' => 'file|max:102400|mimes:mp4',
                'campaign_id' => 'nullable|integer|in:' . implode(',', $campaigns),
                'enterprise_id' => 'nullable|integer|exists:enterprises,id',
            ]);

            $files = $this->request->file('media_files');
            $totalSize = 0;
            $createdMedia = [];

            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            if ($totalSize > 1073741824) {
                throw new \Exception('Total file size exceeds 1GB limit.');
            }

            $enterpriseId = $this->auth->hasRole('root') ? ($data['enterprise_id'] ?? null) : ($this->auth->enterprise_id ?? null);
            if (!$enterpriseId && !$this->auth->hasRole('root')) {
                throw new \Exception('User does not belong to any enterprise.');
            }

            $action = new CreateAction();

            foreach ($files as $file) {
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

                if ($extension !== 'mp4') {
                    throw new \Exception("File $originalFileName is not an MP4 file (invalid extension).");
                }
                if ($extension === 'mp4') {
                    $getID3 = new getID3();
                    $fileInfo = $getID3->analyze($file->getPathname());
                    if (!isset($fileInfo['fileformat']) || $fileInfo['fileformat'] !== 'mp4') {
                        throw new \Exception("File $originalFileName has .mp4 extension but is not a valid MP4 file.");
                    }
                }

                $path = $this->auth->hasRole('root') ? $fileName : "{$enterpriseId}/{$fileName}";
                $mediaUrl = Storage::disk('minio')->url($path);

                if (Storage::disk('minio')->exists($path) || Media::where('media_url', $mediaUrl)->exists()) {
                    throw new \Exception(__('media-create.media-exists', ['name' => $fileName]));
                }

                $getID3 = new getID3();
                $tempPath = $file->getPathname();
                $fileInfo = $getID3->analyze($tempPath);
                Log::info('getID3 file info', ['file' => $fileName, 'info' => $fileInfo]);
                $duration = isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;

                if ($this->auth->hasRole('root')) {
                    $path = Storage::disk('minio')->putFileAs('', $file, $fileName);
                } else {
                    $path = Storage::disk('minio')->putFileAs("{$enterpriseId}", $file, $fileName);
                }

                if (!Storage::disk('minio')->exists($path)) {
                    throw new \Exception('File not found on MinIO after upload: ' . $fileName);
                }

                $mediaUrl = Storage::disk('minio')->url($path);

                $mediaData = [
                    'name' => $data['name'] ?? pathinfo($fileName, PATHINFO_FILENAME),
                    'file_name' => $fileName,
                    'media_url' => $mediaUrl,
                    'size' => $file->getSize(),
                    'type' => $mimeType,
                    'duration' => $duration,
                    'campaign_id' => $data['campaign_id'] ?? null,
                    'enterprise_id' => $enterpriseId,
                ];

                $media = $action->handle($mediaData);
                $createdMedia[] = $media;
            }

            if ($this->request->ajax() || $this->request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('media-create.upload-success', ['count' => count($createdMedia)]),
                    'data' => $createdMedia
                ], 200);
            }

            return $createdMedia;

        } catch (\Exception $e) {
            Log::error('Failed to process file', ['error' => $e->getMessage()]);
            if ($this->request->ajax() || $this->request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }
    }
}