<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Service\Controller;

use App\Domains\FileManager\Model\FileManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class DownloadService
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

    /**
     * DownloadService a file from the FileManager.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function download(int $id): Response
    {
        try {
            // Tìm model
            $model = FileManager::findOrFail($id);

            // Kiểm tra quyền
            if (!$this->auth->hasRole('root') && $model->enterprise_id !== $this->auth->enterprise_id) {
                throw new \Exception(__('file-manager.no-permission'));
            }

            // Lấy đường dẫn file từ model_url
            $parsedUrl = parse_url($model->model_url, PHP_URL_PATH);
            $filePath = ltrim($parsedUrl, '/');
            $bucketName = config('filesystems.disks.minio.bucket');
            $filePath = str_replace($bucketName.'/', '', $filePath);

            // Kiểm tra file tồn tại trên MinIO
            if (!Storage::disk('minio')->exists($filePath)) {
                throw new \Exception(__('file-manager.download-error-not-found'));
            }

            // Lấy file content
            $fileContent = Storage::disk('minio')->get($filePath);
            $fileName = $model->file_name ?? basename($model->model_url);

            // Trả về response tải file
            return response($fileContent, 200, [
                'Content-Type' => $model->type,
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                'Content-Length' => $model->size,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception(__('file-manager.download-error-not-found'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
