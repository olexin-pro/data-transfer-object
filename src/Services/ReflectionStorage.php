<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Services;

use ReflectionClass;
use ReflectionException;

final class ReflectionStorage
{
    private static array $cache = [];
    private const int LIMIT = 300;

    /**
     * @throws ReflectionException
     */
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
