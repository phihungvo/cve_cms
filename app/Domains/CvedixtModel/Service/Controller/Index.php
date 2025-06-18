<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Service\Controller;

use App\Domains\CvedixtModel\Model\CvedixtModel;

class Index
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
        return [
            'model' => $this->getModel(),
        ];
    }

    protected function getModel()
    {
        $query = CvedixtModel::query();

        // Quyền truy cập
        if ($this->auth->hasRole('root')) {
            $query->withTrashed(); // Root thấy cả model đã bị soft delete
        } else {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) {
                return [];
            }
            $query->where('enterprise_id', $enterpriseId)
                ->whereNull('deleted_at'); // Người dùng chỉ thấy model chưa bị soft delete
        }

        // Tìm kiếm nếu có
        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('model_url', 'like', '%'.$search.'%');
            });
        }

        // Sắp xếp theo created_at
        $query->orderBy('created_at', 'asc');

        // Lấy dữ liệu
        $modelItems = $query->get();

        // Chuyển đổi dữ liệu thành mảng
        return $modelItems->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'model_url' => $model->model_url,
                'size' => $model->size,
                'type' => $model->type,
                'created_at' => $model->created_at->timestamp,
                'updated_at' => $model->updated_at ? $model->updated_at->timestamp : null,
                'deleted_at' => $model->deleted_at ? $model->deleted_at->timestamp : null, // Thêm trạng thái soft delete
                'enterprise_id' => $model->enterprise_id,
            ];
        })->all();
    }
}
