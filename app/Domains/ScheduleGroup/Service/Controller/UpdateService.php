<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Service\Controller;

use App\Domains\ScheduleGroup\Model\ScheduleGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected ScheduleGroupModel $row)
    {
        $this->request();
    }

    /**
     * Data update ScheduleGroup
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            // and more ...
        ];
    }
}
