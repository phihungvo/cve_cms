<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class CvedixrtInstanceBuilder extends BuilderAbstract
{
    /**
     * @param int $solutionId
     *
     * @return self
     */
    public function whereBySolution(int $solutionId): self
    {
        if ($solutionId === 0)
        {
            return $this;
        }
        return $this->where('solution_id', $solutionId);
    }

    public function whereByGroup(int $groupId): self
    {
        if ($groupId === 0)
        {
            return $this;
        }
        return $this->where('group_id', $groupId);
    }
}
