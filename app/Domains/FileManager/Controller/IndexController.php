<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Controller;

use App\Domains\FileManager\Action\DeleteAction;
use App\Domains\FileManager\Model\FileManager as Model;
use App\Domains\FileManager\Service\Controller\DownloadService as DownloadService;
use App\Domains\FileManager\Service\Controller\IndexService as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response|JsonResponse
    {
        // Nếu là yêu cầu AJAX hoặc yêu cầu JSON, trả về dữ liệu JSON
        if ($this->request->ajax() || $this->request->wantsJson()) {
            $service = ControllerService::new($this->request, $this->auth);

            return response()->json([
                'success' => true,
                'data' => $service->data(),
            ]);
        }

        $this->meta('title', __('File Management'));

        return $this->page('file-manager.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    /**
     * Get the contents of a folder.
     *
     * @param string $path
     *
     * @return JsonResponse
     */
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
                'enterprise_id' => $item->enterprise_id,
                'is_folder' => $item->is_folder,
                'file_count' => $item->is_folder ? $item->countFilesInFolder() : 0,
            ];
        })]);
    }

    /**
     * DeleteAction files or folders.
     *
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        $modelIdInput = $this->request->input('model_id');

        if (!$modelIdInput) {
            return response()->json([
                'success' => false,
                'message' => 'ID không hợp lệ',
            ], 422);
        }

        $modelIds = array_filter(array_map('intval', is_array($modelIdInput) ? $modelIdInput : explode(',', $modelIdInput)));
        if (empty($modelIds)) {
            return response()->json([
                'success' => false,
                'message' => 'ID không hợp lệ',
            ], 422);
        }

        $action = new DeleteAction();
        $results = [];
        foreach ($modelIds as $modelId) {
            $result = $action->handle($modelId, $this->auth);
            $results[] = $result;
        }

        $allSuccess = array_reduce($results, fn ($carry, $result) => $carry && $result['success'], true);
        $messages = array_column($results, 'message');

        return response()->json([
            'success' => $allSuccess,
            'message' => $allSuccess ? 'Xóa thành công' : implode('; ', array_unique($messages)),
        ]);
    }

    public function download($id): Response
    {
        $service = DownloadService::new($this->request, $this->auth);

        return $service->download((int) $id);
    }
}
