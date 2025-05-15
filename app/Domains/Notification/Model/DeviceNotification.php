<?php

declare(strict_types=1);

namespace App\Domains\Notification\Model;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Device\Model\Device;


class DeviceNotification extends Model
{
    protected $table = 'device_notifications';

    protected $fillable = [
        'notification_id',
        'device_id',
        'playlist_id',
        'is_sent',
        'is_read',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

}