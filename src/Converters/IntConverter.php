<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final class IntConverter implements TypeConverterInterface
{
    public function convert($value): int
    {
        return intval($value);
    }
}