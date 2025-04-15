<?php

namespace App\Domains\User\Role\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;

class Role extends BuilderAbstract
{
//    public function byEnterpriseId(int $enterprise_id):self
//    {
//        return $this->whereIn('id', Enterprise::query()->select('enterprise_id')->byId($enterprise_id));
//    }

    public function list():self
    {
        return $this->orderBy('name', 'ASC');
    }

}
