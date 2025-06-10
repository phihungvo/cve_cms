<?php declare(strict_types=1);

namespace App\Domains\Device\Enums;

enum Direction: string
{
    case BOTH = 'both';
    case UP = 'up';
    case DOWN = 'down';
}
