<?php

namespace App\Domains\Device\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCveditInstance extends Model
{
    protected $table = 'device_cvedit_instance';

    public const TABLE = 'device_cvedit_instance';

    public const ID = 'id';

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(DeviceCvedixSolution::class, 'solution_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(DeviceCveditGroup::class, 'group_id');
    }

    protected function casts(): array
    {
        return [
            'zones' => 'array',
            'lines' => 'array',
        ];
    }
}
