<?php declare(strict_types=1);

namespace App\Domains\Display\Model;

use App\Domains\Display\Model\Builder\DisplayBuilder;
use App\Domains\Display\Model\Collection\DisplayCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Device;
use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\User\Model\User as UserModel; // Thêm import UserModel

class Display extends ModelAbstract
{
    use HasFactory;

    protected $table = 'display';

    public const TABLE = 'display';

    public const PRIMARY = 'id';

    public const FOREIGN = 'display_id';

    public const PLAYLIST_PUBLISHED = 'playlist_published';

    public const SCHEDULE_PUBLISHED = 'schedule_published';

    protected $fillable = [
        'status_id',
        'type',
        'description',
        'location_id',
        'device_id',
        'schedule_id',
        'playlist_published',
        'schedule_published',
        'playlist_id',
    ];

    protected $casts = [
        'playlist_published' => 'integer',
        'schedule_published' => 'integer',
        'notification_published' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, Device::FOREIGN);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, Schedule::FOREIGN);
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(PlaylistModel::class, PlaylistModel::FOREIGN);
    }

    /**
     * Scope để tìm theo ID
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeById($query, int $id)
    {
        return $query->where(self::PRIMARY, $id);
    }

    /**
     * Scope để lọc theo user hoặc manager
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Domains\User\Model\User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserOrManager($query, UserModel $user)
    {
        return $query->when($user->managerMode() === false, function ($q) use ($user) {
            // Giả định: lọc dựa trên device.user_id hoặc một logic tương tự
            $q->whereHas('device', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        });
    }

    public function newCollection(array $models = []): DisplayCollection
    {
        return new DisplayCollection($models);
    }

    public function newEloquentBuilder($query): DisplayBuilder
    {
        return new DisplayBuilder($query);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Notification\Model\Notification::class, 'notification_id');
    }
}
