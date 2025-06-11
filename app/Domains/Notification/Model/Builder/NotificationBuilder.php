<?php

declare(strict_types=1);

namespace App\Domains\Notification\Model\Builder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NotificationBuilder extends Builder
{
    public function filterByPermission(Builder $query, string $alias): Builder
    {
        $user = Auth::user();

        if ($user && $user->isRoleRoot()) {
            return $query;
        } elseif ($user && ($user->isOwner() || $user->hasPermission($alias))) {
            return $query->where(function ($q) use ($user) {
                $q->where('enterprise_id', $user->enterprise_id)
                    ->orWhereNull('enterprise_id');
            });
        }

        return $query->where('id', 0);
    }
}
