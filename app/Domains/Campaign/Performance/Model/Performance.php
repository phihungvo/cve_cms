<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Performance\Model;

use App\Domains\Campaign\Model\Campaign;
use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Performance extends ModelAbstract
{
    use SoftDeletes;

    protected $table = 'performance';

    public const TABLE = 'performance';

    public $timestamps = true;

    protected $fillable = [
        'reach',
        'actual_reach',
        'impression',
        'actual_impression',
        'distance',
        'actual_distance',
        'cpm',
        'actual_cpm',
        'actual_cost',
        'no_device', // Added no_device to fillable
    ];

    protected $casts = [
        'reach' => 'integer',
        'actual_reach' => 'integer',
        'impression' => 'integer',
        'actual_impression' => 'integer',
        'distance' => 'integer',
        'actual_distance' => 'integer',
        'cpm' => 'integer',
        'actual_cpm' => 'integer',
        'actual_cost' => 'decimal:2',
        'no_device' => 'integer', // Added cast for no_device
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Một Performance có thể được sử dụng bởi nhiều Campaign
     *
     * @return HasMany
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'performance_id', 'id');
    }
}
