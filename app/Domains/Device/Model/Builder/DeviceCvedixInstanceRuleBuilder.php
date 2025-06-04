<?php declare(strict_types=1);

namespace App\Domains\Device\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class DeviceCvedixInstanceRuleBuilder extends BuilderAbstract
{
    public function byInstanceId(int $instanceId): self
    {
        return $this->where('device_cvedixrt_instance_id', $instanceId);
    }
}
