<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use App\Domains\CvedixtModel\Action\Delete;
use App\Domains\CvedixtModel\Action\Rename;
use App\Domains\CvedixtModel\Model\CvedixtModel as Model;
use App\Domains\CvedixtModel\Service\Controller\Download as DownloadService;
use App\Domains\CvedixtModel\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('cvedixt Model'));

        return $this->page('cvedixrt.model.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    public function getFolderContents($path): JsonResponse
    {
        $decodedPath = urldecode($path);
        Log::info('Lấy nội dung thư mục', ['path' => $decodedPath]);

        $service = ControllerService::new($this->request, $this->auth);
        $children = $service->getChildren($decodedPath);

        return response()->json(['model' => $children->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'model_url' => $item->model_url,
                'size' => $item->size,
                'type' => $item->type,
                'created_at' => $item->created_at->timestamp,
                'updated_at' => $item->updated_at ? $item->updated_at->timestamp : null,
                'enterprise_id' => $item->enterprise_id,
                'is_folder' => $item->is_folder,
                'file_count' => $item->is_folder ? $item->countFilesInFolder() : 0,
            ];
        })]);
    }

    public function createFolder(): JsonResponse
    {
        try {
            $parentPath = $this->request->input('parent_id', '');
            $folderName = trim($this->request->input('name'));
            if (empty($folderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên thư mục không được để trống',
                ], 422);
            }

            $enterpriseId = $this->auth->hasRole('root') ? null : ($this->auth->enterprise_id ?? null);
            if (!$enterpriseId && !$this->auth->hasRole('root')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền tạo thư mục',
                ], 403);
            }

            $basePath = $parentPath ?: ($enterpriseId ? $enterpriseId : '');
            $path = $basePath ? "{$basePath}/{$folderName}" : $folderName;
            $fullPath = '/'.trim($path, '/');

            if (Storage::disk('minio')->exists($path) || Model::where('model_url', $fullPath)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thư mục đã tồn tại',
                ], 422);
            }

            Storage::disk('minio')->makeDirectory($path);

            $parentId = $parentPath ? Model::where('model_url', '/'.trim($parentPath, '/'))->first()?->id : null;
            $model = Model::create([
                'name' => $folderName,
                'file_name' => $folderName,
                'model_url' => $fullPath,
                'size' => 0,
                'type' => 'folder',
                'enterprise_id' => $enterpriseId,
                'parent_id' => $parentId,
                'is_folder' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo thư mục thành công',
                'data' => [
                    'path' => $path,
                    'fullPath' => $fullPath,
                    'name' => $folderName,
                    'parentPath' => $parentPath,
                    'model_id' => $model->id,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo thư mục: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(): JsonResponse
    {
        $modelIdInput = $this->request->input('model_id');
        Log::info('Nhận yêu cầu xóa', ['model_id_input' => $modelIdInput]);

        if (!$modelIdInput) {
            Log::error('Không nhận được model_id', ['request' => $this->request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'ID không hợp lệ',
            ], 422);
        }

        $modelIds = array_filter(array_map('intval', is_array($modelIdInput) ? $modelIdInput : explode(',', $modelIdInput)));
        if (empty($modelIds)) {
            Log::error('Không có ID hợp lệ sau khi xử lý', ['model_id_input' => $modelIdInput]);
            return response()->json([
                'success' => false,
                'message' => 'ID không hợp lệ',
            ], 422);
        }

        $action = new Delete();
        $results = [];
        foreach ($modelIds as $modelId) {
            $result = $action->handle($modelId, $this->auth);
            $results[] = $result;
            if (!$result['success']) {
                Log::error('Xóa thất bại cho model_id', ['model_id' => $modelId, 'message' => $result['message']]);
            }
        }

        $allSuccess = array_reduce($results, fn ($carry, $result) => $carry && $result['success'], true);
        $messages = array_column($results, 'message');

        return response()->json([
            'success' => $allSuccess,
            'message' => $allSuccess ? 'Xóa thành công' : implode('; ', array_unique($messages)),
        ]);
    }

    public function rename(): RedirectResponse
    {
        $modelId = (int) $this->request->input('model_id');
        $newName = $this->request->input('name');
        $action = new Rename();
        $result = $action->handle($modelId, $newName, $this->auth);

        return $this->redirectResult($result, 'cvedixrt_model.index');
    }

    public function download($id): Response
    {
        $service = DownloadService::new($this->request, $this->auth);
        return $service->download((int) $id);
    }

    protected function redirectResult(array $result, string $route): RedirectResponse
    {
        if ($result['success']) {
            $this->sessionMessage('success', $result['message']);
        } else {
            $this->sessionMessage('error', $result['message']);
        }
        return redirect()->route($route);
    }
}
