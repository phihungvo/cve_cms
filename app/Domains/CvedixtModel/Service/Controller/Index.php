<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Service\Controller;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;

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
            'tree' => $this->buildTree(),
        ];
    }

    protected function getModel()
    {
        $query = CvedixtModel::query();

        if ($this->auth->hasRole('root')) {
            $query->withTrashed();
        } else {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) return [];
            $query->where('enterprise_id', $enterpriseId)->whereNull('deleted_at');
        }

        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('model_url', 'like', "%$search%");
            });
        }

        $query->orderBy('created_at', 'asc');

        return $query->get(); // Trả về collection từ database
    }

    protected function buildTree()
    {
        $models = $this->getModel();
        $tree = [];
        $enterpriseId = $this->auth->hasRole('root') ? null : $this->auth->enterprise_id;

        // Xây dựng cây từ database
        foreach ($models as $model) {
            $parts = explode('/', trim($model->model_url, '/'));
            $current = &$tree;

            for ($i = 0; $i < count($parts); $i++) {
                if (!isset($current[$parts[$i]])) {
                    $current[$parts[$i]] = [];
                }
                $current = &$current[$parts[$i]];
            }
        }

        // Lấy danh sách thư mục từ MinIO
        $disk = Storage::disk('minio');
        $prefix = $enterpriseId ? "{$enterpriseId}/" : '';
        $objects = $disk->allDirectories($prefix); // Lấy tất cả các thư mục trong bucket

        foreach ($objects as $objectPath) {
            $relativePath = $enterpriseId ? str_replace("{$enterpriseId}/", '', $objectPath) : $objectPath;
            $parts = explode('/', trim($relativePath, '/'));
            $current = &$tree;

            for ($i = 0; $i < count($parts); $i++) {
                if (!isset($current[$parts[$i]])) {
                    $current[$parts[$i]] = [];
                }
                $current = &$current[$parts[$i]];
            }
        }

        return $tree;
    }
}
