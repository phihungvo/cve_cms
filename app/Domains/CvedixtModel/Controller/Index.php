<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use App\Domains\CvedixtModel\Action\Create;
use App\Domains\CvedixtModel\Action\Delete;
use App\Domains\CvedixtModel\Action\Rename;
use App\Domains\CvedixtModel\Service\Controller\Download as DownloadService;
use App\Domains\CvedixtModel\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use App\Domains\CvedixtModel\Model\CvedixtModel as Model;
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

    public function getFolderContents($path)
    {
        $service = ControllerService::new($this->request, $this->auth);
        $children = $service->getChildren($path);

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
            ];
        })]);
    }

    public function createFolder(): JsonResponse
    {
        $parentPath = $this->request->input('parent_id'); // Đường dẫn cha từ client
        $folderName = $this->request->input('name', 'Folder_' . date('His')); // Tên thư mục với timestamp
        $enterpriseId = $this->auth->hasRole('root') ? ($this->request->input('enterprise_id') ?? null) : $this->auth->enterprise_id;

        // Xây dựng đường dẫn cho thư mục mới
        $path = $parentPath ? "{$parentPath}/{$folderName}" : ($enterpriseId ? "{$enterpriseId}/{$folderName}" : $folderName);
        $fullPath = '/' . trim($path, '/');

        try {
            // Tạo thư mục trong MinIO
            $disk = Storage::disk('minio');
            $disk->makeDirectory($path); // Tạo thư mục trong bucket

            // Trả về phản hồi để cập nhật giao diện
            return response()->json([
                'success' => true,
                'message' => 'Thư mục đã được tạo',
                'path' => $path,
                'fullPath' => $fullPath,
                'name' => $folderName,
                'parentPath' => $parentPath, // Gửi lại parentPath để đồng bộ
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo thư mục: ' . $e->getMessage(),
            ], 500);
        }
    }

//    public function createFolder(): JsonResponse
//    {
//        $parentPath = $this->request->input('parent_id'); // Đường dẫn cha
//        $folderName = $this->request->input('name', 'NewFolder_'.\Illuminate\Support\Str::random(5));
//        $enterpriseId = $this->auth->hasRole('root') ? ($this->request->input('enterprise_id') ?? null) : $this->auth->enterprise_id;
//
//        // Xây dựng đường dẫn cho thư mục mới
//        $path = $parentPath ? "{$parentPath}/{$folderName}" : ($enterpriseId ? "{$enterpriseId}/{$folderName}" : $folderName);
//        $fullPath = '/'.trim($path, '/');
//
//        try {
//            // Tạo thư mục trong MinIO
//            $disk = Storage::disk('minio');
//            $disk->makeDirectory($path); // Tạo thư mục trong bucket
//
//            // Trả về phản hồi để cập nhật giao diện
//            return response()->json([
//                'success' => true,
//                'message' => 'Thư mục đã được tạo',
//                'path' => $path,
//                'fullPath' => $fullPath,
//                'name' => $folderName,
//            ]);
//        } catch (\Exception $e) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Lỗi khi tạo thư mục: '.$e->getMessage(),
//            ], 500);
//        }
//    }

    public function destroy(): RedirectResponse
    {
        $mediaUrl = $this->request->input('model_url');
        $action = new Delete();
        $result = $action->handle($mediaUrl, $this->auth);

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
