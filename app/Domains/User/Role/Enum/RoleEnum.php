<?php

namespace App\Domains\User\Role\Enum;

enum RoleEnum: string
{
    case ROOT = 'root';
    case OWNER = 'owner';
}
