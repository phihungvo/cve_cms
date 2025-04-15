<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Model;

use App\Domains\Campaign\Media\Model\Media;
use App\Domains\Campaign\Performance\Model\Performance;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Model\User;
use App\Domains\Campaign\Location\Model\Location;
use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Campaign extends ModelAbstract
{
    use SoftDeletes;

    protected $table = 'campaign';

    /**
     * @const string
     */
    public const TABLE = 'campaign';

    /**
     * @const string
     */
    const FOREIGN = 'campaign_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'budget',
        'status',
        'performance_id',
        'enterprise_id',
        'location_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Một Campaign có nhiều Media
     *
     * @return HasMany
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'campaign_id', 'id')->withTrashed();
    }

    /**
     * Một Campaign thuộc về một User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Một Campaign thuộc về một Performance
     *
     * @return BelongsTo
     */
    public function performance(): BelongsTo
    {
        return $this->belongsTo(Performance::class, 'performance_id', 'id');
    }

    /**
     * Một Campaign thuộc về một Enterprise
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }

    /**
     * Một Campaign thuộc về một Location
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    /**
     * Scope để lọc campaign theo enterprise
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEnterprise(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        if ($user && !$user->isRoleRoot()) {
            return $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }
    /**
     * Một Campaign có nhiều User
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_campaign',
            'campaign_id',
            'user_id'
        )->withTimestamps();
    }
}
