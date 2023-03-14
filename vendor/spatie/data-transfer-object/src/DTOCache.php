<?php

namespace Spatie\DataTransferObject;

use Closure;

class DTOCache
{
    /** @var array */
    private static $cache = [];

    public static function resolve(string $class, Closure $closure): array
    {
        if (! isset(self::$cache[$class])) {
            self::$cache[$class] = $closure();
        }

        return self::$cache[$class];
    }
}
