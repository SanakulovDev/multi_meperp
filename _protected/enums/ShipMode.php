<?php

namespace app\enums;

use Yii;

class ShipMode
{
    public const CONTAINER = 1;
    public const TRUCK = 2;
    public const AIR = 3;

    public static function list(): array
    {
        return [
            self::CONTAINER => 'Container',
            self::TRUCK => 'Truck',
            self::AIR => 'Avia'
        ];
    }

    public static function name($shipMode): string
    {
        return self::list()[$shipMode];
    }
}
