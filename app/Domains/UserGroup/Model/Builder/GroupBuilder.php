<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Model\Builder;
use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;

class GroupBuilder extends BuilderAbstract{

    public function roleRoot()
    {
        if(auth()->user()->isRoleRoot()){
            return $this->withTrashed();
        }
        return $this;
    }

    public function roleOwner()
    {
        if(auth()->user()->isOwner()){
            return $this->where('enterprise_id', auth()->user()->enterprise_id);
        }
        return $this;
    }

    public function getEnterpriseName():self
    {
        return $this->with([Enterprise::TABLE => function ($query) {
            $query->select('id', 'name');
        }]);
    }
}
