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
        Log::info('Received create media request', [
            'method' => $this->request->method(),
            'url' => $this->request->url(),
            'headers' => $this->request->headers->all(),
        ]);

        try {
            $campaigns = Campaign::all()->pluck('id')->toArray();

            $data = $this->request->validate([
                'media_files' => 'required|array|max:10',
                'media_files.*' => 'file|max:102400|mimes:mp4',
                'campaign_id' => 'nullable|integer|in:' . implode(',', $campaigns),
                'enterprise_id' => $this->auth->hasRole('root') ? 'nullable|integer|exists:enterprise,id' : 'required|integer|exists:enterprise,id',
            ]);

            $files = $this->request->file('media_files');
            $totalSize = 0;
            $createdMedia = [];

            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            if ($totalSize > 1073741824) {
                throw new \Exception('Tổng dung lượng file vượt quá giới hạn 1GB.');
            }

            // Xử lý enterprise_id
            $enterpriseId = $this->auth->hasRole('root') ? ($data['enterprise_id'] ?? null) : ($this->auth->enterprise_id ?? null);
            if (!$this->auth->hasRole('root') && !$enterpriseId) {
                throw new \Exception('Người dùng không thuộc doanh nghiệp nào.');
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
                    throw new \Exception("File $originalFileName không phải là file MP4 (đuôi file không hợp lệ).");
                }
                if ($extension === 'mp4') {
                    $getID3 = new getID3();
                    $fileInfo = $getID3->analyze($file->getPathname());
                    if (!isset($fileInfo['fileformat']) || $fileInfo['fileformat'] !== 'mp4') {
                        throw new \Exception("File $originalFileName có đuôi .mp4 nhưng không phải là file MP4 hợp lệ.");
                    }
                }

                try {
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
                        throw new \Exception('Không tìm thấy file trên MinIO sau khi upload: ' . $fileName);
                    }

                    $mediaUrl = Storage::disk('minio')->url($path);

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

                    $media = $action->handle($mediaData);
                    $createdMedia[] = $media;

                } catch (\Exception $e) {
                    Log::error('Failed to process file', [
                        'file' => $originalFileName,
                        'error' => $e->getMessage(),
                        'user_id' => $this->auth->id,
                        'role' => $this->auth->hasRole('root') ? 'root' : 'non-root',
                    ]);
                    throw new \Exception('Không thể upload file ' . $originalFileName . '. Lỗi: ' . $e->getMessage());
                }
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
            Log::error('Failed to process file', [
                'error' => $e->getMessage(),
                'user_id' => $this->auth->id,
                'role' => $this->auth->hasRole('root') ? 'root' : 'non-root',
            ]);
            if ($this->request->ajax() || $this->request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            throw $e;
        }
    }
}