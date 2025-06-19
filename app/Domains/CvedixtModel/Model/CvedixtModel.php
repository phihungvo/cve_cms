<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\CvedixtModel\Model\Builder\ModelBuilder;
use App\Domains\CvedixtModel\Model\Collection\ModelCollection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\User\Enterprise\Model\Enterprise;

class CvedixtModel extends ModelAbstract
{
    use SoftDeletes;

    /**
     * @const string
     */
    const PRIMARY = 'id';

    protected $table = 'cvedixt_model';

    /**
     * @const string
     */
    public const TABLE = 'cvedixt_model';

    /**
     * @const string
     */
    const FOREIGN = 'model_id';

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
     * Tạo một instance của collection tùy chỉnh, thay vì sử dụng Collection mặc định
     * Không cần gọi phương thức này, Laravel sẽ tự động gọi khi cần thiết
     *
     * @param array $models
     *
     * @return ModelCollection
     *
     * @see \Illuminate\Database\Eloquent\Model::newCollection()
     *
     * @overide
     */
    public function newCollection(array $models = []): ModelCollection
    {
        return new ModelCollection($models);
    }

    /**
     * Tạo một instance của Eloquen buider tùy chỉnh, thay vì sử dụng Eloquent mặc định
     * Không cần phải override phương thức này, Laravel sẽ tự động gọi khi cần thiết
     *
     * @param $query
     *
     * @return ModelBuilder
     *
     * @see \Illuminate\Database\Eloquent\Model::newEloquentBuilder()
     *
     * @override
     */
    public function newEloquentBuilder($query): ModelBuilder
    {
        return new ModelBuilder($query);
    }
}
