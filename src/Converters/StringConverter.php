<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final class StringConverter implements TypeConverterInterface
{
    public function convert($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        throw new \TypeError(
            sprintf("Cannot convert %s to string", get_debug_type($value))
        );
    }
}
