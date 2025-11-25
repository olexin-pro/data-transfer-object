<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class CollectionConverter implements TypeConverterInterface
{
    public function convert($value): ?Collection
    {
        if (is_null($value)) {
            return null;
        }

        if(!is_array($value)) {
            throw new InvalidArgumentException('Collection converter requires an array');
        }
        return collect($value);
    }
}
