<?php

declare(strict_types=1);

namespace App\Domains\Notification\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use Illuminate\Support\Facades\Auth;

class NotificationBuilder extends BuilderAbstract
{
    public function filterByPermission(string $alias): self
    {
        if (auth()->user()->isRoleRoot()) {
            return $this;
        } elseif (auth()->user()->hasPermission($alias)) {
            return $this->where('enterprise_id', auth()->user()->enterprise_id);

        }

        return $this->where('id', 0);

    }
}
