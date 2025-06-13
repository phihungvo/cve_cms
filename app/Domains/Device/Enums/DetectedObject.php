<?php declare(strict_types=1);

namespace App\Domains\Device\Enums;

enum DetectedObject: string
{
    case PERSON = 'person';
    case VEHICLE = 'vehicle';
    case ANIMAL = 'animal';
    case UNKNOWN = 'unknown';

    public function subTypes(): array
    {
        return match($this) {
            self::PERSON => ['head', 'helmet', 'adult', 'child'],
            self::VEHICLE => ['car', 'bike', 'truck'],
            self::ANIMAL => ['dog', 'cat', 'bird'],
            self::UNKNOWN => [],
        };
    }
}
