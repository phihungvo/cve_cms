<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class CvedixrtInstanceRuleBuilder extends BuilderAbstract
{
    /**
     * @return self
     */
    public function listSimple(): self
    {
        return $this->select('id', 'name')->orderBy('name', 'ASC');
    }
}
