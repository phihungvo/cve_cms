<?php

namespace App\Domains\Report\Model;

use Illuminate\Database\Eloquent\Model;

class ViewLog extends Model
{
    // Tên bảng
    protected $table = 'view_logs';

    // Khóa chính
    protected $primaryKey = 'id';

    // Có tự động tăng
    public $incrementing = true;

    // Kiểu khóa chính
    protected $keyType = 'int';

    // Tự động quản lý timestamps (created_at, updated_at)
    public $timestamps = false;

    // Các cột được phép gán hàng loạt
    protected $fillable = [
        'device_id',
        'serial',
        'media_filename',
        'view_count',
        'frame_data',
        'created_at',
    ];
}
