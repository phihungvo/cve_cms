<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Model;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\CoreApp\Model\ModelAbstract;

class ScheduleGroupMap extends ModelAbstract
{
    protected $table = 'schedule_group_map';

    public const TABLE = 'schedule_group_map';

    public const PRIMARY = 'id';

    public const FOREIGN = 'schedule_group_map_id';

    protected $fillable = [
        'schedule_id',
        'schedule_group_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function scheduleGroup(): BelongsTo
    {
        return $this->belongsTo(ScheduleGroupModel::class, ScheduleGroupModel::FOREIGN);
    }
}
