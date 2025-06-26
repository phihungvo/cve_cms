<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Service\Controller;

use App\Domains\FileManager\Action\CreateFolderAction as CreateFolderAction;
use Illuminate\Http\JsonResponse;

class CreateFolder
{
    protected $request;
    protected $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * CreateAction a new instance of the service.
     *
     * @param mixed $request
     * @param mixed $auth
     * @return self
     */
    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    /**
     * Handle folder creation logic.
     *
     * @return JsonResponse
     */
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

            $enterpriseId = $this->auth->hasRole('root') ? null : ($this->auth->enterprise_id ?? null);
            if (!$enterpriseId && !$this->auth->hasRole('root')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền tạo thư mục',
                ], 403);
            }

            $parentPath = $this->request->input('parent_id', '');
            $action = new CreateFolderAction();
            $model = $action->handle([
                'folder_name' => $folderName,
                'parent_path' => $parentPath,
                'enterprise_id' => $enterpriseId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo thư mục thành công',
                'data' => [
                    'path' => $parentPath ? "{$parentPath}/{$folderName}" : $folderName,
                    'fullPath' => $model->model_url,
                    'name' => $folderName,
                    'parentPath' => $parentPath,
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
