<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final class ArrayConverter implements TypeConverterInterface
{
    public function convert(mixed $value): array
    {
        if (is_string($value) && json_validate($value)) {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        return (array) $value;
    }
}