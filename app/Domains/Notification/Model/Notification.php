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
use Illuminate\Database\Eloquent\Builder;
use App\Domains\Notification\Model\UserNotification;

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

    /**
     * Tạo một instance của NotificationBuilder
     */
    public function newEloquentBuilder($query): NotificationBuilder
    {
        return new NotificationBuilder($query);
    }

    /**
     * Scope cho vai trò Root
     */
    public function scopeRoleRoot(Builder $query): Builder
    {
        if (auth()->user()->hasRole('root')) {
            return $query; // Không thêm điều kiện lọc cho Root
        }

        return $query;
    }

    /**
     * Scope cho vai trò Owner
     */
    public function scopeRoleOwner(Builder $query): Builder
    {
        if (auth()->user()->isOwner()) {
            return $query->where('enterprise_id', auth()->user()->enterprise_id);
        }

        return $query;
    }

    /**
     * Scope để lọc theo quyền của người dùng
     */
    public function scopeFilterByPermission(Builder $query, string $alias): Builder
    {
        if (auth()->user()->hasRole('root')) {
            return $query;
        } elseif (auth()->user()->hasPermission($alias)) {
            return $query->where('enterprise_id', auth()->user()->enterprise_id);
        }

        return $query->where('id', 0);
    }
}