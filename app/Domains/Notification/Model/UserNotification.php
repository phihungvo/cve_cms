<?php

declare(strict_types=1);

namespace App\Domains\Notification\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends ModelAbstract
{
    use HasFactory;

    protected $table = 'user_notification';

    public const TABLE = 'user_notification';

    protected $fillable = [
        'user_id',
        'notification_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}