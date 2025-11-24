<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Illuminate\Support\Fluent;
use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final class FluentConverter implements TypeConverterInterface
{
    public function convert(mixed $value): ?Fluent
    {
        if (!is_array($value)) {
            return null;
        }

        return Fluent::make($value);
    }

}