<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Services;

use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;
use Ol3x1n\DataTransferObject\Converters\ArrayConverter;
use Ol3x1n\DataTransferObject\Converters\BooleanConverter;
use Ol3x1n\DataTransferObject\Converters\CollectionConverter;
use Ol3x1n\DataTransferObject\Converters\DateConverter;
use Ol3x1n\DataTransferObject\Converters\DynamicConverter;
use Ol3x1n\DataTransferObject\Converters\FloatConverter;
use Ol3x1n\DataTransferObject\Converters\FluentConverter;
use Ol3x1n\DataTransferObject\Converters\IntConverter;
use Ol3x1n\DataTransferObject\Converters\StringConverter;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;

final class ConverterResolver
{
    private static array $instances = [];

    /**
     * @param TypeEnum|class-string<TypeConverterInterface>|string $type
     */
    public function resolve(TypeEnum|string $type): TypeConverterInterface
    {
        $key = $type instanceof TypeEnum ? $type->value : $type;
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }
        $instance = $this->createInstance($type);
        self::$instances[$key] = $instance;
        return $instance;
    }

    private function createInstance(TypeEnum|string $type): TypeConverterInterface
    {
        if ($type instanceof TypeEnum) {
            return match ($type) {
                TypeEnum::ARRAY => new ArrayConverter(),
                TypeEnum::COLLECTION => new CollectionConverter(),
                TypeEnum::INT => new IntConverter(),
                TypeEnum::FLOAT => new FloatConverter(),
                TypeEnum::STRING => new StringConverter(),
                TypeEnum::DATE => new DateConverter(),
                TypeEnum::BOOLEAN => new BooleanConverter(),
                TypeEnum::FLUENT => new FluentConverter(),
                default => new DynamicConverter(),
            };
        }

        if (is_subclass_of($type, TypeConverterInterface::class)) {
            return new $type();
        }

        return new DynamicConverter();
    }
}
