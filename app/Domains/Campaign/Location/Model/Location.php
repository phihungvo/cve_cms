<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Location\Model;

use App\Domains\Campaign\Model\Campaign;
use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends ModelAbstract
{
    use SoftDeletes;

    protected $table = 'location';

    public const TABLE = 'location';

    public $timestamps = true;

    protected $fillable = [
        'latitude',
        'longitude',
        'city',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Một Location có thể được sử dụng bởi nhiều Campaign
     *
     * @return HasMany
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'location_id', 'id');
    }
}
