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
    private array $instances = [];

    /**
     * @param TypeEnum|class-string $type
     * @return TypeConverterInterface
     */
    public function resolve(TypeEnum|string $type): TypeConverterInterface
    {
        if (is_string($type) && is_subclass_of($type, TypeConverterInterface::class)) {
            return new $type();
        }
        if ($type instanceof TypeEnum === false) {
            return new DynamicConverter();
        }
        return $this->resolveEnumConverter($type);
    }

    private function resolveEnumConverter(TypeEnum $type): TypeConverterInterface
    {
        if (!isset($this->instances[$type->value])) {
            $this->instances[$type->value] = match ($type) {
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
        return clone $this->instances[$type->value];
    }
}