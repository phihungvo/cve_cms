<?php

declare(strict_types=1);

namespace App\Domains\Notification\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Notification\Model\Builder\NotificationBuilder;
use App\Domains\User\Model\User;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Device\Model\Device;

class Notification extends ModelAbstract
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notification';

    public const TABLE = 'notification';
    public const FOREIGN = 'notification_id';

    protected $fillable = [
        'title',
        'content',
        'notification_type',
        'enterprise_id',
        'sender_id',
        'target_group',
        'created_at',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'notification_type' => 'string',
        'created_at' => 'datetime',
    ];

    /**
     * Get a new query builder instance for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return NotificationBuilder
     */
    public function newQueryBuilder($query): NotificationBuilder
    {
        return new NotificationBuilder($query);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'notification_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'enterprise_id', 'enterprise_id');
    }
}