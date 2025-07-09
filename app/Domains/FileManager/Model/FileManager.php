<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\FileManager\Model\Builder\FileManagerBuilder as Builder;
use App\Domains\FileManager\Model\Collection\FileManagerCollection as Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\User\Enterprise\Model\Enterprise;

class FileManager extends ModelAbstract
{
    /**
     * @const string
     */
    const PRIMARY = 'id';

    protected $table = 'file_manager';

    /**
     * @const string
     */
    public const TABLE = 'file_manager';

    /**
     * @const string
     */
    const FOREIGN = 'file_manager_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'file_name',
        'model_url',
        'size',
        'type',
        'enterprise_id',
        'parent_id',
        'is_folder',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'size' => 'integer',
        'is_folder' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Model có thể là một thư mục hoặc một tệp
     * Nếu là thư mục, thì không có file_name và model_url
     * Nếu là tệp, thì có file_name và model_url
     *
     * @var bool
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Lấy tất cả các con của một Model
     * Nếu là thư mục, thì sẽ có nhiều con
     * Nếu là tệp, thì sẽ không có con
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Một Model thuộc về một enterprise
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(
            Enterprise::class,
            Enterprise::FOREIGN_KEY,
            self::PRIMARY
        );
    }

    /**
     * Đếm số file trực tiếp trong thư mục (không tính subfolder).
     *
     * @return int
     */
    public function countFilesInFolder(): int
    {
        return FileManager::where('model_url', 'like', $this->model_url.'/%')
            ->where('is_folder', false)
            ->count();
    }

    /**
     * Tạo một instance của collection tùy chỉnh, thay vì sử dụng Collection mặc định
     * Không cần gọi phương thức này, Laravel sẽ tự động gọi khi cần thiết
     *
     * @param array $models
     *
     * @return Collection
     *
     * @see \Illuminate\Database\Eloquent\Model::newCollection()
     *
     * @overide
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * Tạo một instance của Eloquen buider tùy chỉnh, thay vì sử dụng Eloquent mặc định
     * Không cần phải override phương thức này, Laravel sẽ tự động gọi khi cần thiết
     *
     * @param $query
     *
     * @return Builder
     *
     * @see \Illuminate\Database\Eloquent\Model::newEloquentBuilder()
     *
     * @override
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }
}
