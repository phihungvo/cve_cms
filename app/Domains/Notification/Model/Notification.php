<?php

declare(strict_types=1);

namespace App\Domains\Notification\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Model\User;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}