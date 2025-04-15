<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Service\Controller;

use App\Domains\Campaign\Media\Model\Media;

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
            'media' => $this->getMedia(),
        ];
    }

    protected function getMedia()
    {
        $query = Media::query();

        // Quyền truy cập
        if ($this->auth->hasRole('root')) {
            $query->withTrashed(); // Root thấy cả media đã bị soft delete
        } else {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) {
                return [];
            }
            $query->where('enterprise_id', $enterpriseId)
                ->whereNull('deleted_at'); // Người dùng chỉ thấy media chưa bị soft delete
        }

        // Tìm kiếm nếu có
        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('media_url', 'like', '%' . $search . '%');
            });
        }

        // Sắp xếp theo created_at
        $query->orderBy('created_at', 'asc');

        // Lấy dữ liệu
        $mediaItems = $query->get();

        // Chuyển đổi dữ liệu thành mảng
        return $mediaItems->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'media_url' => $media->media_url,
                'size' => $media->size,
                'type' => $media->type,
                'duration' => $media->duration,
                'created_at' => $media->created_at->timestamp,
                'updated_at' => $media->updated_at ? $media->updated_at->timestamp : null,
                'deleted_at' => $media->deleted_at ? $media->deleted_at->timestamp : null, // Thêm trạng thái soft delete
                'enterprise_id' => $media->enterprise_id,
            ];
        })->all();
    }
}