<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use App\Domains\CvedixtModel\Action\Delete;
use App\Domains\CvedixtModel\Action\Rename;
use App\Domains\CvedixtModel\Model\CvedixtModel;
use App\Domains\CvedixtModel\Service\Controller\Download as DownloadService;
use App\Domains\CvedixtModel\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('Cvedixt Model'));

        return $this->page('cvedixrt.model.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    public function getFolderContents($path): JsonResponse
    {
        $decodedPath = urldecode($path);

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
                'deleted_at' => $item->deleted_at ? $item->deleted_at->timestamp : null,
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
            $fullPath = '/' . trim($path, '/');

            // Kiểm tra thư mục đã tồn tại
            if (Storage::disk('minio')->exists($path) || CvedixtModel::where('model_url', $fullPath)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thư mục đã tồn tại',
                ], 422);
            }

            // Tạo thư mục trên MinIO
            Storage::disk('minio')->makeDirectory($path);

            // Lưu vào database với parent_id đúng
            $parentId = $parentPath ? CvedixtModel::where('model_url', '/' . trim($parentPath, '/'))->first()?->id : null;
            $model = CvedixtModel::create([
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
                'message' => 'Lỗi khi tạo thư mục: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(): RedirectResponse
    {
        $modelId = $this->request->input('model_id'); // Lấy ID từ request
        if (!$modelId || !is_numeric($modelId)) {
            $this->sessionMessage('error', 'ID không hợp lệ');
            return redirect()->route('cvedixrt_model.index');
        }

        $action = new Delete();
        $result = $action->handle((int)$modelId, $this->auth);

        return $this->redirectResult($result, 'cvedixrt_model.index');
    }

    public function restore($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->restore($id, $this->auth);

        return $this->redirectResult($result, 'cvedixrt_model.index');
    }

    public function forceDelete($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->forceDelete($id, $this->auth);

        return $this->redirectResult($result, 'cvedixrt_model.index');
    }

    public function rename(): RedirectResponse
    {
        $modelId = $this->request->input('model_id');
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
