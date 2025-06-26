<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Service\Controller;

use App\Domains\FileManager\Action\CreateFolderAction;
use Illuminate\Http\JsonResponse;

class CreateFolderService
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

    public function create(): JsonResponse
    {
        try {
            $folderName = trim($this->request->input('name'));
            if (empty($folderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên thư mục không được để trống',
                ], 422);
            }

            $enterpriseId = $this->auth->hasRole('root') ? null : $this->auth->enterprise_id;
            if (!$this->auth->hasRole('root') && !$enterpriseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Người dùng không thuộc bất kỳ doanh nghiệp nào',
                ], 403);
            }

            $parentPath = $this->request->input('parent_id', '');
            // Chuẩn hóa parentPath để bao gồm enterprise_id
            $fullParentPath = $enterpriseId && $parentPath ? "{$enterpriseId}/{$parentPath}" : ($enterpriseId ? $enterpriseId : $parentPath);

            $action = new CreateFolderAction();
            $model = $action->handle([
                'folder_name' => $folderName,
                'parent_path' => $fullParentPath,
                'enterprise_id' => $enterpriseId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo thư mục thành công',
                'data' => [
                    'path' => $fullParentPath ? "{$fullParentPath}/{$folderName}" : $folderName,
                    'fullPath' => $model->model_url,
                    'name' => $folderName,
                    'parentPath' => $fullParentPath,
                    'model_id' => $model->id,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
