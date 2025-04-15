<?php

declare(strict_types=1);

namespace App\Domains\Video\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder; // Import Builder để dùng trong scope
use App\Domains\CoreApp\Model\ModelAbstract;

class Video extends ModelAbstract
{
    use HasFactory;

    protected $table = 'video';

    protected $fillable = [
        'name',
        'description',
        'filename',
        'thumbnail_url',
        'publish_time',
        'start_time',
        'end_time',
        'logo_url',
        'video_url',
        'reach_target',
        'distance_target',
        'impression_target',
        'device_target',
        'cpm_target',
        'cost',
        'video_type',
        'enabled',
    ];

    /**
     * Scope để lọc video theo ID
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeById(Builder $query, int $id): Builder
    {
        return $query->where('id', $id);
    }

    /**
     * Scope để lọc video theo người dùng hoặc quản lý
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $auth
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserOrManager(Builder $query, $auth): Builder
    {
        if (!$auth) {
            return $query; // Nếu không có auth, không lọc gì cả
        }

        // Giả định: bảng 'video' có cột 'user_id' để liên kết với người dùng
        // Nếu $auth là quản lý, cho phép xem tất cả; nếu không, chỉ xem của user
        if ($auth->isManager()) { // Giả định có method isManager() trong $auth
            return $query;
        }

        return $query->where('user_id', $auth->id); // Lọc theo user_id
    }
}
