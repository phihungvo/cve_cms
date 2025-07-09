<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Service\Controller;

use App\Domains\FileManager\Action\CreateAction;
use App\Domains\FileManager\Model\FileManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Aws\S3\S3Client;

class CreateService
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
     *
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
                'enterprise_id' => $this->auth->hasRole('root') ? 'nullable|integer|exists:enterprise,id'
                    : 'required|integer|exists:enterprise,id',
            ]);

            $files = $this->request->file('model_files') ?? [];
            $parentPath = $data['parent_id'] ?? '';
            $totalSize = 0;
            $createdModel = [];
            $errors = [];

            if (empty($files)) {
                throw new \Exception('Không có file nào được tải lên.');
            }

            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            if ($totalSize > 2147483648) {
                throw new \Exception('Tổng kích thước file vượt quá giới hạn 2GB.');
            }

            // Xác định enterprise_id
            $enterpriseId = $this->auth->hasRole('root') ? null : $this->auth->enterprise_id;
            if ($parentPath && $this->auth->hasRole('root')) {
                // Nếu là root và có parentPath, lấy enterprise_id từ thư mục cha
                $parentModel = FileManager::where('model_url', '/'.trim($parentPath, '/'))
                    ->where('is_folder', true)
                    ->first();
                if ($parentModel) {
                    $enterpriseId = $parentModel->enterprise_id;
                }
            }
            if (!$enterpriseId && !$this->auth->hasRole('root')) {
                throw new \Exception('Người dùng không thuộc bất kỳ doanh nghiệp nào.');
            }

            // Kiểm tra kết nối MinIO
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
                throw new \Exception('Không thể kết nối với server lưu trữ: '.$e->getMessage());
            }

            $action = new CreateAction();

            // Xử lý từng file
            foreach ($files as $index => $file) {
                try {
                    $originalFileName = $file->getClientOriginalName();
                    $fileName = $this->sanitizeFileName($originalFileName);
                    $mimeType = $file->getMimeType();

                    // Chuẩn hóa đường dẫn: luôn bắt đầu bằng enterprise_id nếu có
                    $basePath = $enterpriseId ? $enterpriseId.($parentPath ? "/{$parentPath}" : '') : $parentPath;
                    $path = $basePath ? "{$basePath}/{$fileName}" : $fileName;
                    $modelUrl = '/'.trim($path, '/');

                    // Kiểm tra file đã tồn tại
                    if (Storage::disk('minio')->exists($path) || FileManager::where('model_url', $modelUrl)->exists()) {
                        throw new \Exception(__('File đã tồn tại!', ['name' => $fileName]));
                    }

                    // Lưu file vào MinIO với đường dẫn chuẩn hóa
                    $storedPath = Storage::disk('minio')->putFileAs($basePath, $file, $fileName);
                    if (!Storage::disk('minio')->exists($storedPath)) {
                        throw new \Exception('File không tồn tại trên MinIO sau khi upload: '.$fileName);
                    }

                    // Cập nhật modelUrl với đường dẫn chính xác
                    $modelUrl = '/'.trim($storedPath, '/');

                    $modelData = [
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'file_name' => $fileName,
                        'model_url' => $modelUrl,
                        'size' => $file->getSize(),
                        'type' => $mimeType,
                        'enterprise_id' => $enterpriseId,
                        'parent_id' => $parentPath ? FileManager::where('model_url', '/'.trim($basePath, '/'))->first()?->id : null,
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
                    'message' => $errors[0]['error'] ?? 'Một số file không thể tải lên.',
                    'errors' => $errors,
                    'data' => $createdModel,
                ];

                return $this->request->ajax() || $this->request->wantsJson()
                    ? response()->json($response, 207)
                    : $createdModel;
            }

            $response = [
                'success' => true,
                'message' => __('file-manager.upload_success', ['count' => count($createdModel)]),
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
