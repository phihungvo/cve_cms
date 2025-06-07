<?php declare(strict_types=1);

namespace App\Domains\Device\Enums;

enum DetectedObject: string
{
    case PERSON = 'person';
    case VEHICLE = 'vehicle';
    case ANIMAL = 'animal';
    case UNKNOWN = 'unknown';
}
