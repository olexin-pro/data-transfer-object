<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Services;

use ReflectionClass;

final class ReflectionStorage
{
    private static array $cache = [];
    private const LIMIT = 200;

    public static function get(string $class): ReflectionClass
    {
        if (!isset(self::$cache[$class])) {

            if (count(self::$cache) >= self::LIMIT) {
                array_shift(self::$cache);
            }

            self::$cache[$class] = new ReflectionClass($class);
        }

        return self::$cache[$class];
    }
}