<?php declare(strict_types=1);

namespace App\Domains\Device\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class DeviceCvedixInstanceBuilder extends BuilderAbstract
{
    /**
     * @param int $deviceId
     *
     * @return $this|self
     */
    public function whereByDevice(int $deviceId): self
    {
        if ($deviceId === 0) {
            return $this;
        }

        return $this->where('device_id', $deviceId);
    }

    /**
     * @param int $solutionId
     *
     * @return self
     */
    public function whereBySolution(int $solutionId): self
    {
        if ($solutionId === 0) {
            return $this;
        }

        return $this->where('solution_id', $solutionId);
    }

    /**
     * @param int $groupId
     *
     * @return $this|self
     */
    public function whereByGroup(int $groupId): self
    {
        if ($groupId === 0) {
            return $this;
        }

        return $this->where('group_id', $groupId);
    }
}
