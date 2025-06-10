<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Enums;

enum RuleType: string
{
    case INTRUSION_DETECTION = 'instruction_detection';
    case AREA_ENTER_EXIT = 'area_enter_exit';
    case LOITERING = 'loitering';
    case CROWDING = 'crowding';
    case LINE_CROSSING = 'line_crossing';
}
