<?php declare(strict_types=1);


namespace App\Domains\Cvedixrt\Instance\Enums;

enum Direction: string
{
    case BOTH = 'both';
    case UP = 'up';
    case DOWN = 'down';
}
